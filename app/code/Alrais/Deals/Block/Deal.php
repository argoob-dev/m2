<?php

namespace Alrais\Deals\Block;

use Magento\Framework\View\Element\Template;

class Deal extends Template {

    protected $dealFactory;
    protected $_resource;
    protected $_productCollection;
    protected $formkey;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Alrais\Deals\Model\DealFactory $dealFactory
    ) {
        $this->dealFactory = $dealFactory;
        $this->_productCollection = $productCollection;
        $this->formkey = $formkey;
        $this->_datetime=$datetime;
        $this->_resource = $resource;
        parent::__construct($context);
    }
    

    public function getSelectedProducts($dealId) {
        $collection = $this->_productCollection->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $this->getDealProductIds($dealId)))
                ->load();
        return $collection;
    }

    public function getDeal() {
        $deal = $this->dealFactory->create()->getCollection()
                ->addFieldToSelect('*')
                ->addOrder('creation_time', 'DESC')
                ->setPageSize(1)
                ->load();
        return $deal;
    }
    public function getDealProductIds($dealId)
    {
        $tableName = $this->_resource->getTableName(\Alrais\Deals\Model\ResourceModel\Deal::TBL_ATT_PRODUCT);
        $select = $this->_resource->getConnection()->select()->from(
            $tableName,
            ['product_id']
        )
        ->where(
            'deal_id = ?',
            (int)$dealId
        );
        return $this->_resource->getConnection()->fetchCol($select);
    }
    public function getFormKey() {
        return $this->formkey->getFormKey();
    }
    public function getDealProducts(){

        // $now = $this->_datetime->gmtDate();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $this->_productCollection->create()->addAttributeToSelect('*');       
        $collection->addAttributeToSelect('strikethrough_price')
        // ->addAttributeToSelect('special_to_date')
        ->addAttributeToFilter('strikethrough_price', ['neq' => '']);
        // $collection->addAttributeToSelect('special_from_date')
        // ->addAttributeToSelect('special_to_date');
        // $collection->addAttributeToFilter(
        //     array(
        //             array('attribute'=> 'special_price','neq' => ''),
        //             array('attribute'=> 'special_from_date','lteq' => date('Y-m-d H:i:s', strtotime($now))),
        //             array('attribute'=> 'special_to_date','gteq' => date('Y-m-d H:i:s', strtotime($now))),
        //             array('attribute'=> 'offer_label','neq' =>'')
        //         )
        // );
        return $collection;
    }
}
