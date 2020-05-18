<?php

namespace Alrais\VehicleFilter\Controller\Year;

class YearList extends \Magento\Framework\App\Action\Action {

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection
    ) {
        $this->vehicleCollection = $vehicleCollection;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
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
            $years->setOrder('year','DESC');
            return $result->setData($years->getData());
        }
    }

}
