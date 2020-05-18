<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Vehicle;

class Vehicles extends \Alrais\VehicleFilter\Controller\Adminhtml\Vehicle {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Product\Collection $productVehicleCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productVehicleCollection = $productVehicleCollection;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $productId = $this->getRequest()->getParam('product_id');
            $vehicles = $this->productVehicleCollection
                    ->addFieldToFilter('entity_id', $productId);
            $vehicles->getSelect()->joinLeft(
                    ['year' => $vehicles->getTable('alrais_vehicle_year')], 'main_table.year_id = year.id', ['year'=>'year.year']);
            return $result->setData($vehicles->getData());
        }
    }

}
