<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Year;

class Year extends \Alrais\VehicleFilter\Controller\Adminhtml\Year {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->vehicleCollection = $vehicleCollection;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $makeId = $this->getRequest()->getParam('make_id');
            $modelId = $this->getRequest()->getParam('model_id');
            $years = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToFilter('model_id', $modelId)
                    ->addFieldToSelect('year_id');
            
            $years->getSelect()->group('year_id');
            $years->getSelect()->joinLeft(
                    ['year' => $years->getTable('alrais_vehicle_year')], 'main_table.year_id = year.id', ['year'=>'year.year']);
            $years->setOrder('year','ASC');
            return $result->setData($years->getData());
        }
    }

}
