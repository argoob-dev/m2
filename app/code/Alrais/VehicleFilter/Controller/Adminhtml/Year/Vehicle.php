<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Year;

class Vehicle extends \Alrais\VehicleFilter\Controller\Adminhtml\Year {

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
            $this->saveVehicle();
            $makeId = $this->getRequest()->getParam('make_id');
            $modelId = $this->getRequest()->getParam('model_id');
            $yearId = $this->getRequest()->getParam('year_id');
            $vehicle = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToFilter('model_id', $modelId)
                    ->addFieldToFilter('year_id', $yearId);

            $vehicle->getSelect()->joinLeft(
                    ['make' => $vehicle->getTable('alrais_vehicle_make')], 'main_table.make_id = make.id', []);
            $vehicle->getSelect()->joinLeft(
                    ['model' => $vehicle->getTable('alrais_vehicle_model')], 'main_table.model_id = model.id', []);
            $vehicle->getSelect()->joinLeft(
                    ['year' => $vehicle->getTable('alrais_vehicle_year')], 'main_table.year_id = year.id', ['vehicle' => 'concat(make.make_en, " >> ", model.model_en," >> ",year.year)']);
            return $result->setData($vehicle->getFirstItem()->getData());
        }
    }

    private function saveVehicle() {
        $makeId = $this->getRequest()->getParam('make_id');
        $modelId = $this->getRequest()->getParam('model_id');
        $yearId = $this->getRequest()->getParam('year_id');
        $vehicle = $this->_objectManager->create('Alrais\VehicleFilter\Model\Resource\Vehicle\Collection')
                ->addFieldToFilter('make_id', $makeId)
                ->addFieldToFilter('model_id', $modelId)
                ->addFieldToFilter('year_id', $yearId);
        if (!$vehicle->getFirstItem()->getId()) {
            try {
                $model = $this->_objectManager->create('Alrais\VehicleFilter\Model\Vehicle');
                $model->setMakeId($makeId);
                $model->setModelId($modelId);
                $model->setYearId($yearId);
                $model->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                        __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
    }

}
