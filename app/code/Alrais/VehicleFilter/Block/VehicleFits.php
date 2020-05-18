<?php

namespace Alrais\VehicleFilter\Block;

use Magento\Framework\View\Element\Template;

class VehicleFits extends Template {


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Alrais\VehicleFilter\Model\Resource\Make\Collection $makeCollection,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->makeCollection = $makeCollection;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    public function getMakes($productId = null) {
        if($productId){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $makes = $objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection');
            $makes->addFieldToFilter('entity_id', $productId);
            $makes->getSelect()->group('entity_mapping.make_id');
            $makes->getSelect()->joinLeft(
                ['entity_mapping' => $makes->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
            $makes->getSelect()->joinLeft(
                ['make' => $makes->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id', ['make'=>'make.make_en','make_id'=>'make.id']);
            return $makes;
        }else{
            return $this->makeCollection;
        }
    }
    public function getModels($makeId, $productId = null) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $models = $objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection');
        $models->addFieldToFilter('entity_mapping.make_id', $makeId);
        $models->getSelect()->group('entity_mapping.model_id');
        $models->getSelect()->joinLeft(
            ['entity_mapping' => $models->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
        $models->getSelect()->joinLeft(
            ['make' => $models->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id', ['make'=>'make.make_en','make_id'=>'make.id']);
        $models->getSelect()->joinLeft(
            ['model' => $models->getTable('alrais_vehicle_model')], 'entity_mapping.model_id = model.id', ['model'=>'model_en','model_id'=>'model.id']);
        if($productId){
            $models->addFieldToFilter('entity_id', $productId);
        }
        return $models;
    }
    public function getYears($makeId, $modelId, $productId = null) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $years = $objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection');
        $years->addFieldToFilter('entity_mapping.make_id', $makeId);
        $years->addFieldToFilter('entity_mapping.model_id', $modelId);
        $years->getSelect()->group('entity_mapping.year_id');
        $years->getSelect()->joinLeft(
            ['entity_mapping' => $years->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
        $years->getSelect()->joinLeft(
            ['make' => $years->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id', ['make'=>'make.make_en', 'make_id'=>'make.id']);
        $years->getSelect()->joinLeft(
            ['model' => $years->getTable('alrais_vehicle_model')], 'entity_mapping.model_id = model.id', ['model'=>'model.model_en', 'model_id'=>'model.id']);
        $years->getSelect()->joinLeft(
            ['year' => $years->getTable('alrais_vehicle_year')], 'entity_mapping.year_id = year.id', ['year'=>'year.year', 'year_id'=>'year.id']);
        if($productId){
            $years->addFieldToFilter('entity_id', $productId);
        }
        $years->setOrder('year','DESC');
        return $years;
    }

    public function getCurrentProduct() {       
        return $this->_registry->registry('current_product');
    }   

}
