<?php

namespace Alrais\ProductWidget\Block\PopularItems;

class Products extends \Magento\Framework\View\Element\Template {

    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Framework\Stdlib\DateTime\DateTime $datetime, \Magento\Sales\Model\Order\Status\History $orderstatus, \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection, \Magento\CatalogInventory\Helper\Stock $stockHelper, \Magento\Review\Model\ReviewFactory $reviewFactory, \Magento\Review\Model\ResourceModel\Review\Summary\Collection $ratingCollection, \Magento\Catalog\Model\ProductFactory $_productloader, array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->_coreResource = $resource;
        $this->urlHelper = $urlHelper;
        $this->_datetime = $datetime;
        $this->_orderstatus = $orderstatus;
        $this->productCollection = $productCollection;
        $this->ratingCollection = $ratingCollection;
        $this->stockHelper = $stockHelper;

        $this->_reviewFactory = $reviewFactory;
        $this->_productloader = $_productloader;
        parent::__construct(
                $context, $data
        );
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();

        if ($this->getProducts()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager')->setAvailableLimit(array(5 => 5, 10 => 10, 15 => 15, 20 => 20));
            $pager->setLimit(10)->setShowPerPage(true);
            $pager->setCollection($this->getProducts());
            $this->setChild('pager', $pager);
            $this->getProducts()->load();
        }
        return $this;
    }

    public function getProducts() {
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')
                        ->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('popularitems', '1');
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        return $collection;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

}
