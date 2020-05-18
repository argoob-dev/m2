<?php

namespace Alrais\ProductWidget\Block\NewArrivals;

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
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        // $collection = $this->_productCollectionFactory->create()
        //                 ->addAttributeToSelect('*')->addAttributeToFilter('status', '1')
        //                 ->addStoreFilter()->addAttributeToFilter(
        //                 'news_from_date', [
        //             'or' => [
        //                 0 => ['date' => true, 'to' => $todayEndOfDayDate],
        //                 1 => ['is' => new \Zend_Db_Expr('null')],
        //             ]
        //                 ], 'left'
        //         )->addAttributeToFilter(
        //                 'news_to_date', [
        //             'or' => [
        //                 0 => ['date' => true, 'from' => $todayStartOfDayDate],
        //                 1 => ['is' => new \Zend_Db_Expr('null')],
        //             ]
        //                 ], 'left'
        //         )->addAttributeToFilter(
        //                 [
        //                     ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
        //                     ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
        //                 ]
        //         )->addAttributeToSort(
        //                 'news_from_date', 'desc'
        //         )->setPageSize(10)->setCurPage(
        //         1
        // );

        $collection->addAttributeToFilter('new_arrivals', ['eq' => 1]);

        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
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

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

}
