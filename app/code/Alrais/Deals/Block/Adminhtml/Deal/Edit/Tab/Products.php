<?php

namespace Alrais\Deals\Block\Adminhtml\Deal\Edit\Tab;

use Alrais\Deals\Model\DealFactory;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Deal factory
     *
     * @var DealFactory
     */
    protected $dealFactory;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;
    protected $_objectManager = null;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param DealFactory $attachmentFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Registry $registry, \Magento\Framework\ObjectManagerInterface $objectManager, DealFactory $dealFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, array $data = []
    ) {
        $this->dealFactory = $dealFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('deal_id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
    }

    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection() {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToFilter('visibility', 4);
        $collection->addAttributeToFilter('special_price',['neq'=>null], 'left');
        $collection->addAttributeToFilter('special_from_date', ['or' => [0 => ['date' => true,'to' => date('Y-m-d', time()).' 23:59:59'],1 => ['is' => new \Zend_Db_Expr('null')],]], 'left');
        $collection->addAttributeToFilter('special_to_date',  ['or' => [ 0 => ['date' => true,'from' => date('Y-m-d',time()).' 00:00:00'],1 => ['is' => new \Zend_Db_Expr('null')],]], 'left');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns() {
        /* @var $model \Alrais\Deals\Model\Slide */
        $model = $this->_objectManager->get('\Alrais\Deals\Model\Deal');

        $this->addColumn(
                'in_product', [
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_product',
            'align' => 'center',
            'index' => 'entity_id',
            'values' => $this->_getSelectedProducts(),
                ]
        );

        $this->addColumn(
                'entity_id', [
            'header' => __('Product ID'),
            'type' => 'number',
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
                ]
        );
        $this->addColumn(
                'name', [
            'header' => __('Name'),
            'index' => 'name',
            'class' => 'xxx',
            'width' => '50px',
                ]
        );
        $this->addColumn(
                'sku', [
            'header' => __('Sku'),
            'index' => 'sku',
            'class' => 'xxx',
            'width' => '50px',
                ]
        );
        $this->addColumn(
                'price', [
            'header' => __('Price'),
            'type' => 'currency',
            'index' => 'price',
            'width' => '50px',
                ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row) {
        return '';
    }

    protected function _getSelectedProducts() {
        $deal = $this->getDeal();
        return $deal->getProducts($deal);
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts() {
        $deal = $this->getDeal();
        $selected = $deal->getProducts($deal);

        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }

    protected function getDeal() {
        $dealId = $this->getRequest()->getParam('deal_id');
        $deal = $this->dealFactory->create();
        if ($dealId) {
            $deal->load($dealId);
        }
        return $deal;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return true;
    }

}
