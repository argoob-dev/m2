<?php

namespace Alrais\VehicleFilter\Controller\Vehicle;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action {

    protected $helper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection, \Alrais\VehicleFilter\Helper\Data $helper
    ) {
        $this->vehicleCollection = $vehicleCollection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute() {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $makeId = $this->getRequest()->getParam('make_id');
        $modelId = $this->getRequest()->getParam('model_id');
        $yearId = $this->getRequest()->getParam('year_id');
        $vehicleId = $this->getRequest()->getParam('vehicle_id');
        if ($makeId && $modelId && $yearId) {
            $vehicle = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToFilter('model_id', $modelId)
                    ->addFieldToFilter('year_id', $yearId);
        } else {
            $vehicle = $this->vehicleCollection
                    ->addFieldToFilter('main_table.id', $vehicleId);
        }
        $vehicle->getSelect()->joinLeft(
                ['make' => $vehicle->getTable('alrais_vehicle_make')], 'main_table.make_id = make.id', []);
        $vehicle->getSelect()->joinLeft(
                ['model' => $vehicle->getTable('alrais_vehicle_model')], 'main_table.model_id = model.id', []);
        $vehicle->getSelect()->joinLeft(
                ['year' => $vehicle->getTable('alrais_vehicle_year')], 'main_table.year_id = year.id', ['year' => 'year.year', 'make' => 'make.make_en', 'model' => 'model.model_en',
            'image' => 'concat(UPPER(REPLACE(model.model_en, " ", "-")),"-", year.year)']);

        $vehicleInfo = ["vehicle_id" => $vehicle->getFirstItem()->getId(), "make" => $vehicle->getFirstItem()->getMake(), "model" => $vehicle->getFirstItem()->getModel(), "year" => $vehicle->getFirstItem()->getYear(), "vehicle_image" => $vehicle->getFirstItem()->getImage()];
        $this->helper->addVehicle(json_encode($vehicleInfo));

        $this->_objectManager->create('Psr\Log\LoggerInterface')->info($vehicle->getSelect()->__toString());
        $vehicleList = $this->helper->getRecentVehicles();
        if ($vehicleList) {
            $vehicleList = (array) json_decode($vehicleList, true);

            $vehicleList[] = $vehicleInfo;
            $vehicleList = array_unique($vehicleList, SORT_REGULAR);
        } else {
            $vehicleList[] = $vehicleInfo;
        }
        $this->helper->addToRecentVehicles(json_encode($vehicleList));
        // $this->helper->clearCache();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

}
