<?php

namespace Alrais\VehicleFilter\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsaveafter implements ObserverInterface
{    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger
    ) { 
        $this->_request = $request;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_request = $objectManager->create('Magento\Framework\App\RequestInterface');
        $_product = $observer->getProduct();  
        $data = $_request->getPost();
        if (isset($data["product"]["vehicle"])) {
            if ($data["product"]["vehicle"] != "") {
                try {
                    $vehicles = $data["product"]["vehicle"];
                    $vehicleIds = explode(',', $vehicles);
                    $this->deleteVehicles($_product->getId());
                    foreach($vehicleIds as $vehicleId){
                        $vehicle = $objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection')
                                ->addFieldToFilter('make_model_year_id', $vehicleId)
                                ->addFieldToFilter('entity_id',  $_product->getId());
                        if (!$vehicle->getFirstItem()->getId()) {
                            $model = $objectManager->create('Alrais\VehicleFilter\Model\Product');
                            $model->setMakeModelYearId($vehicleId);
                            $model->setEntityId($_product->getId());
                            $model->save();
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical(__('Something went wrong while saving the item data. Please review the error log.'));
                    $this->_logger->critical($e->getMessage());
                } catch (\Exception $e) {
                    $this->_logger->critical(__('Something went wrong while saving the item data. Please review the error log.'));
                    $this->_logger->critical($e->getMessage());
                }
            }
        }
    }   

    private function deleteVehicles($productId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $vehicleList = $objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection')
                                ->addFieldToFilter('entity_id',  $productId);
        try {
            foreach ($vehicleList as $vehicle) {
                $model = $objectManager->create('Alrais\VehicleFilter\Model\Product');
                $model->load($vehicle->getId());
                $model->delete();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical(__('Something went wrong while saving the item data. Please review the error log.'));
            $this->_logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical(__('Something went wrong while saving the item data. Please review the error log.'));
            $this->_logger->critical($e->getMessage());
        }
    }
}