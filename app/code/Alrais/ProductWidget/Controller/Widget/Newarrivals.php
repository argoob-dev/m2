<?php

namespace Alrais\ProductWidget\Controller\Widget;

use Magento\Framework\Controller\ResultFactory;

class Newarrivals extends \Magento\Framework\App\Action\Action {

    protected $helper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsFactory,
    \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
    \Alrais\ProductDetails\Helper\Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->_productsFactory = $productsFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        $this->localeDate = $localeDate;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        $todayStartOfDayDate = $this->localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection = $this->_productsFactory->create()->addAttributeToSelect('*');
        // $collection = $this->_productsFactory->create()
        //     ->addAttributeToSelect('*')->addAttributeToFilter('status', '1')
        //     ->addStoreFilter()->addAttributeToFilter(
        //             'news_from_date', [
        //         'or' => [
        //             0 => ['date' => true, 'to' => $todayEndOfDayDate],
        //             1 => ['is' => new \Zend_Db_Expr('null')],
        //         ]
        //             ], 'left'
        //     )->addAttributeToFilter(
        //             'news_to_date', [
        //         'or' => [
        //             0 => ['date' => true, 'from' => $todayStartOfDayDate],
        //             1 => ['is' => new \Zend_Db_Expr('null')],
        //         ]
        //             ], 'left'
        //     )->addAttributeToFilter(
        //             [
        //                 ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
        //                 ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
        //             ]
        //     )->addAttributeToSort(
        //             'news_from_date', 'desc'
        //     );
        $collection->addAttributeToFilter('new_arrivals', ['eq' => 1]);
        $collection->addAttributeToFilter('new_arrivals_on_homepage', ['eq' => 1]);
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 6;
        $collection->setPageSize($pageSize);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $html = $this->getHtml($collection);
        return $result->setData($html);
    }

    private function getHtml($items){
        $productHelper = $this->_objectManager->create('Alrais\ProductDetails\Helper\Data');
        $quickviewHelper = $this->_objectManager->create('Alrais\Quickview\Helper\Data');
        $iterator = 0;
        $limit = 4;
        $html  = '<div class="container"><div class="flex_block"><div class="thirty cat_bg headstyle"><div><h2>'
            . __('NEW ARRIVALS') .'</h2><a href="'. $this->_url->getUrl().'newarrivals' .'" class="btn">'.__('View All') .'</a></div></div>'
            .'<div class="seventy"><ul class="productlisting">';
        
        foreach ($items as $item){
            if ($iterator < $limit) { 
                $html .= '<li><div class="badges_blk">';
                if ($item->getOfferLabel()){
                    $html .='<span class="blue">'. __($item->getAttributeText('offer_label')).'</span>';
                }
                if ($item->getTypeId() == 'bundle'){
                    $html .= '<span class="blue">'. __('Bundle').'</span>';
                }
                if ($item->getSpecialPrice()){
                    $html .= '<span class="red">'. __('DEAL') .'</span>';
                }
                $localDate = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                $newsFromDate = $item->getNewsFromDate();
                $newsToDate = $item->getNewsToDate();
                if (!$newsFromDate && !$newsToDate) {
                    $showNewLabel = false;
                }else{
                    if($localDate->isScopeDateInInterval(
                    $item->getStore(),
                    $newsFromDate,
                    $newsToDate
                    )){
                    $showNewLabel = true;
                    }else{
                    $showNewLabel = false;
                    }
                }
                if ($showNewLabel){
                    $html .= '<span class="green">'. __('NEW').'</span>';
                }
                $html .= '</div><div class="imageholder_pr"><a href="' . $item->getProductUrl() .'"><img src="' 
                    . $productHelper->getImage($item) .'" /></a></div><div class="buttons">'
                    . $productHelper->getAddToCart($item) . $quickviewHelper->getQuickView($item) 
                    . '</div><div class="product_nme"><a href="' . $item->getProductUrl() .'">' .$item->getName()
                    . '</a></div><div class="prices">';
                if ($productHelper->getDiscountPercentage($item) > 0){
                    // $html .= '<span class="actaual_price">' . $productHelper->getOriginalPrice($item) .'</span>';
                }

                if ($item->getCustomAttribute('bundle_original_price')){
                    // $html .= '<span class="actaual_price">'. $item->getCustomAttribute('bundle_original_price')->getValue() . '</span>';
                }

                if ($item->getCustomAttribute('strikethrough_price')){
                    $html .= '<span class="actaual_price">'. $item->getCustomAttribute('strikethrough_price')->getValue() . '</span>';
                }

                $html .= '<span class="special_price">' . $productHelper->getFinalPrice($item) .'</span>';

                $html .= '<div class="stockstaus">';
                if ($item->isAvailable()){ 
                    $html .= '<i class="fa fa-check-square-o"></i><span>'. /* @escapeNotVerified */ __('In stock') .'</span></div>';
                } else{ 
                    $html .= '<i class="fa fa-square-o"></i><span>'. /* @escapeNotVerified */ __('Out of stock') .'</span></div>';
                } 
                
                $html .= '</div></li>';
            }
            $iterator++;
        }
        $html .= '</ul></div></div></div>';
        return $html;
    }
}
