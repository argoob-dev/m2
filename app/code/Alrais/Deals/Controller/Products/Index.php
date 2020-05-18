<?php
namespace Alrais\Deals\Controller\Products;

class Index extends \Magento\Framework\App\Action\Action {

    public function __construct(
            \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_productCollection = $productCollection;
        $this->_storeManager = $storeManager;
        $this->catalogProductVisibility = $catalogProductVisibility;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $store_id = $this->_storeManager->getStore()->getId();
            $collection = $this->_productCollection->create()->addAttributeToSelect('*');       
            $collection->addAttributeToSelect('strikethrough_price')
                ->addAttributeToSelect('homepage');
               $collection->addAttributeToFilter('strikethrough_price', ['neq' => '']);
                $collection->addAttributeToFilter('homepage', ['eq' => 1]);
            $collection->setPageSize(4);
            $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
            return $result->setData($this->renderHtml($collection));
        }
    }

    private function renderHtml($items){
        $productHelper = $this->_objectManager->create('Alrais\ProductDetails\Helper\Data');
        $quickviewHelper = $this->_objectManager->create('Alrais\Quickview\Helper\Data');
        $iterator = 0;
        $limit = 4;
        $html  = '<div class="container"><div class="flex_block reverse"><div class="thirty cat_bg headstyle"><div><h2>'
            . __('GREAT DEALS') .'</h2><a href="'. $this->_url->getUrl().'deals' .'" class="btn">'.__('View All') .'</a></div></div>'
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
                if ($item->getCustomAttribute('strikethrough_price')){
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
                    // $html .= '<span class="actaual_price bundle">AED '. $item->getCustomAttribute('bundle_original_price')->getValue() . '</span>';
                }

                if ($item->getCustomAttribute('strikethrough_price')){
                    $html .= '<span class="actaual_price simple">AED '. $item->getCustomAttribute('strikethrough_price')->getValue().'</span>';
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

    private function getMediaDirectoryUrl() {
        $media_dir = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
}
