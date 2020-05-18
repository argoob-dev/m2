<?php

namespace Alrais\ProductWidget\Controller\Widget;

use Magento\Framework\Controller\ResultFactory;

class Popular extends \Magento\Framework\App\Action\Action {

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
        $collection = $this->_productsFactory->create()
            ->addAttributeToSelect('*')->addAttributeToFilter('status', '1')
            ->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('popularitems', '1');
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 6;
        $collection->setPageSize($pageSize);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $html = $this->getHtml($collection);
        return $result->setData($html);
    }

    private function getHtml($items){
        $iterator = 0;
        $limit = 4;
        $html = '';
        foreach ($items as $item){
            if ($iterator < $limit) {
                $html .= '<li><a href="'. $item->getProductUrl() .'"><div class="image_holder">'
                .'<img src="'. $this->helper->getImage($item) .'" /></div>'
                .'<div class="product_contn"><div class="productname">'. $item->getName()
                .'</div><div class="price_cart">'. $this->helper->getAddToCart($item);
                if ($this->helper->getDiscountPercentage($item) > 0){
                    // $html .= '<span class="actaual_price">'. $this->helper->getOriginalPrice($item) .'</span>';
                }

                if ($item->getCustomAttribute('bundle_original_price')){
                    // $html .= '<span class="actaual_price">'. $item->getCustomAttribute('bundle_original_price')->getValue() . '</span>';
                }

                if ($item->getCustomAttribute('strikethrough_price')){
                    $html .= '<span class="actaual_price">'. $item->getCustomAttribute('strikethrough_price')->getValue() . '</span>';
                }

                $html .= '<span class="special_price">'. $this->helper->getFinalPrice($item) .'</span>';
                $html .= '<div class="stockstaus">';
                
                if ($item->isAvailable()){ 
                    $html .= '<i class="fa fa-check-square-o"></i><span>'. /* @escapeNotVerified */ __('In stock') .'</span></div>';
                } else{ 
                    $html .= '<i class="fa fa-square-o"></i><span>'. /* @escapeNotVerified */ __('Out of stock') .'</span></div>';
                } 
                
                $html .= '</div></li>';
            }
        }
        return $html;
    }
}
