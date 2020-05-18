<?php

namespace Alrais\ProductWidget\Block\MostViewed;

class Products extends \Magento\Framework\View\Element\Template {

    public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
     \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
      \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
      \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
       \Magento\Framework\Data\Form\FormKey $formkey, 
       \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->formkey = $formkey;
        $this->localeDate = $localeDate;
        $this->_productsFactory = $productsFactory;
        parent::__construct(
                $context, $data
        );
    }

    public function getProducts() {
        $currentStoreId = $this->_storeManager->getStore()->getId();

        $collection = $this->_productsFactory->create()
            ->addAttributeToSelect(
                '*'
            )->addViewsCount()->setStoreId(
                    $currentStoreId
            )->addStoreFilter(
                    $currentStoreId
            );
           
        $collection->setPageSize(4);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        return $collection;
    }
}
