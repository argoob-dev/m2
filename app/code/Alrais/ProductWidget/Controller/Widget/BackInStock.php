<?php

namespace Alrais\ProductWidget\Controller\Widget;

use Magento\Framework\Controller\ResultFactory;

class BackInStock extends \Magento\Framework\App\Action\Action {

    protected $helper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsFactory,
    \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Alrais\ProductDetails\Helper\Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->_productsFactory = $productsFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        $collection = $this->getProducts();
        $html = $this->getHtml($collection);
        return $result->setData($html);
    }
    
    public function getProducts() {
        //changed to featured_product from back_in_stock for testing
	$collection = $this->_productsFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', '1')
                ->addAttributeToFilter('featured_product','1');
                $collection->addAttributeToFilter('back_in_stock_on_homepage', ['eq' => 1]);
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 6;
        $collection->setPageSize($pageSize);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        return $collection;
    }

    private function getHtml($items){
        $productHelper = $this->_objectManager->create('Alrais\ProductDetails\Helper\Data');
        $quickviewHelper = $this->_objectManager->create('Alrais\Quickview\Helper\Data');
        $iterator = 0;
        $limit = 4;
        $html  = '<div class="container"><div class="flex_block"><div class="thirty cat_bg headstyle"><div><h2>'
            . __('Back In Stock') .'</h2><a href="'. $this->_url->getUrl().'backinstock' .'" class="btn">'.__('View All') .'</a></div></div>'
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
