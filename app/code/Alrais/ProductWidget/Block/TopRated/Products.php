<?php

namespace Alrais\ProductWidget\Block\TopRated;

class Products extends \Magento\Framework\View\Element\Template {

    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Framework\Data\Form\FormKey $formkey, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->formkey = $formkey;
        $this->localeDate = $localeDate;
        parent::__construct(
                $context, $data
        );
    }

    public function getProducts() {

        $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', '1')
                ->addAttributeToSort('created_at', 'desc');
        
        $_rating = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewFactory = $objectManager->get(\Magento\Review\Model\ReviewFactory::class);
        foreach ($collection as $product) {
            $reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
            $ratingSummary = $product->getRatingSummary()->getRatingSummary();
            if($ratingSummary!=null){
                array_push($_rating,array(
                    'rating' => $ratingSummary,
                    'product' => $product->getId()
                ));
            }
        }
        usort($_rating, function($a, $b) {
            return $a['rating'] <= $b['rating'];
        });
        return array_column($_rating, 'product');
    }
    
    public function getTopRatedProducts() {
        $productIds = $this->getProducts();
        $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', '1')
                ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
            
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->order("find_in_set(e.entity_id,'".implode(',',$productIds)."')");
        return $collection;
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();

        if ($this->getTopRatedProducts()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');
            $pager->setLimit(10)->setShowPerPage(true);
            $pager->setCollection($this->getTopRatedProducts());
            $this->setChild('pager', $pager);
            $this->getTopRatedProducts()->load();
        }
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    public function getFormKey() {
        return $this->formkey->getFormKey();
    }
}
