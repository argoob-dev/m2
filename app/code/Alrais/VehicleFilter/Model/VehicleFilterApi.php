<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Model;

class VehicleFilterApi extends \Magento\Framework\Model\AbstractModel
{
	
	public function __construct(
		\Alrais\VehicleFilter\Model\Resource\Make\Collection $makeCollection,
		\Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection,
		\Alrais\VehicleFilter\Model\Resource\Product\Collection $productCollection,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
		$this->makeCollection = $makeCollection;
		$this->vehicleCollection = $vehicleCollection;
		$this->productCollection = $productCollection;
		$this->storeManager = $storeManager;
	}
	
    /**
	*
	* @api
	* @return $this
	*/
	public function getMake(){
        $makes = $this->makeCollection;
		$makeList = [];
		$makeLabel = "make_". $this->getStore()->getCode();
        foreach($makes as $make){
			$makeItem = $make->getData();
            array_push($makeList, ["id"=> $makeItem['id'], "make"=> $makeItem[$makeLabel]]);
        }
        return $makeList;
    }
	
	/**
	*
	* @api
	* @param int $makeId
	* @return $this
	*/
	public function getModel($makeId){
            $models = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToSelect('model_id');
			$models->getSelect()->group('model_id');
			$modelLabel = "model.model_". $this->getStore()->getCode();
			$models->getSelect()->reset(\Zend_Db_Select::COLUMNS);
			$models->addFieldToFilter($modelLabel, array("neq"=>""));
            $models->getSelect()->joinLeft(
                    ['model' => $models->getTable('alrais_vehicle_model')], 'main_table.model_id = model.id', ['model'=> $modelLabel,'id'=>'model.id']);
            return $models->getData();
    }
	

	/**
	*
	* @api
	* @param int $makeId
	* @param int $modelId
	* @return $this
	*/
	public function getYear($makeId, $modelId){
		$years = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToFilter('model_id', $modelId)
                    ->addFieldToSelect('year_id');
            
		$years->getSelect()->group('year_id');
		$years->getSelect()->reset(\Zend_Db_Select::COLUMNS);
		$years->getSelect()->joinLeft(
				['year' => $years->getTable('alrais_vehicle_year')], 'main_table.year_id = year.id', ['year'=>'year.year','id'=>'year.id']);
		$years->setOrder('year','DESC');
		return $years->getData();
    }
	
	/**
	*
	* @api
	* @param int $productId
	* @return $this
	*/
	public function getProductVehicleMake($productId){
		$makeLabel = "make_". $this->getStore()->getCode();
		$makes = $this->productCollection;
		$makes->addFieldToFilter('entity_id', $productId);
		$makes->getSelect()->group('entity_mapping.make_id');
		$makes->getSelect()->joinLeft(
			['entity_mapping' => $makes->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
		$makes->getSelect()->reset(\Zend_Db_Select::COLUMNS);
		$makes->getSelect()->joinLeft(
			['make' => $makes->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id', ['make'=> $makeLabel, 'id'=>'make.id']);
        return $makes->getData();
    }
	
	/**
	*
	* @api
	* @param int $productId
	* @param int $makeId
	* @return $this
	*/
	public function getProductVehicleModel($productId, $makeId){
		$modelLabel = "model_". $this->getStore()->getCode();
		$models = $this->productCollection;
		$models->addFieldToFilter('entity_mapping.make_id', $makeId);
        $models->getSelect()->group('entity_mapping.model_id');
        $models->getSelect()->joinLeft(
            ['entity_mapping' => $models->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
        $models->getSelect()->joinLeft(
            ['make' => $models->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id');
		$models->getSelect()->reset(\Zend_Db_Select::COLUMNS);
		$models->getSelect()->joinLeft(
            ['model' => $models->getTable('alrais_vehicle_model')], 'entity_mapping.model_id = model.id', ['model'=> $modelLabel, 'id'=>'model.id']);
        $models->addFieldToFilter('entity_id', $productId);
		return $models->getData();
    }

	/**
	*
	* @api
	* @param int $productId
	* @param int $makeId
	* @param int $modelId
	* @return $this
	*/
	public function getProductVehicleYear($productId, $makeId, $modelId){
		$years = $this->productCollection;
		$years->addFieldToFilter('entity_mapping.make_id', $makeId);
        $years->addFieldToFilter('entity_mapping.model_id', $modelId);
        $years->getSelect()->group('entity_mapping.year_id');
        $years->getSelect()->joinLeft(
            ['entity_mapping' => $years->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
        $years->getSelect()->joinLeft(
            ['make' => $years->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id');
        $years->getSelect()->joinLeft(
            ['model' => $years->getTable('alrais_vehicle_model')], 'entity_mapping.model_id = model.id');
		
		$years->getSelect()->reset(\Zend_Db_Select::COLUMNS);
		$years->getSelect()->joinLeft(
			['year' => $years->getTable('alrais_vehicle_year')], 'entity_mapping.year_id = year.id', ['year'=>'year.year', 'id'=>'year.id']);
		$years->setOrder('year','DESC');
		return $years->getData();
    }

	/**
	*
	* @api
	* @param int $makeId
	* @param int $modelId
	* @param int $yearId
	* @return $this
	*/
	public function getVehicle($makeId, $modelId, $yearId){
		$vehicle = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToFilter('model_id', $modelId)
					->addFieldToFilter('year_id', $yearId);
		$modelLabel = "model.model_". $this->getStore()->getCode();
		$makeLabel = "make.make_". $this->getStore()->getCode();
        $vehicle->getSelect()->joinLeft(
                ['make' => $vehicle->getTable('alrais_vehicle_make')], 'main_table.make_id = make.id', []);
        $vehicle->getSelect()->joinLeft(
                ['model' => $vehicle->getTable('alrais_vehicle_model')], 'main_table.model_id = model.id', []);
        $vehicle->getSelect()->joinLeft(
                ['year' => $vehicle->getTable('alrais_vehicle_year')], 'main_table.year_id = year.id', ['year' => 'year.year', 'make' => $makeLabel , 'model' => $modelLabel]);
        $vehicle->addFieldToFilter($makeLabel, array("neq"=>""));
        $vehicle->addFieldToFilter($modelLabel, array("neq"=>""));
        $vehicle->addFieldToFilter('year.year', array("neq"=>""));
		return $vehicle->getData();
	}
	
	/**
	*
	* @api
	* @param int $productId
	* @return $this
	*/
	public function getVehicleFitsProduct($productId){
		$modelLabel = "model.model_". $this->getStore()->getCode();
		$makeLabel = "make.make_". $this->getStore()->getCode();
        $vehicle = $this->productCollection->addFieldToFilter('entity_id', $productId);
        $vehicle->getSelect()->joinLeft(
                ['vehicle' => $vehicle->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = vehicle.id', []);
        $vehicle->getSelect()->joinLeft(
                ['make' => $vehicle->getTable('alrais_vehicle_make')], 'vehicle.make_id = make.id', []);
        $vehicle->getSelect()->joinLeft(
                ['model' => $vehicle->getTable('alrais_vehicle_model')], 'vehicle.model_id = model.id', []);
        $vehicle->getSelect()->joinLeft(
                ['year' => $vehicle->getTable('alrais_vehicle_year')], 'vehicle.year_id = year.id', ['year' => 'year.year', 'make' => $makeLabel, 'model' => $modelLabel]);
		$vehicle->addFieldToFilter($makeLabel, array("neq"=>""));
		$vehicle->addFieldToFilter($modelLabel, array("neq"=>""));
        $vehicle->addFieldToFilter('year.year', array("neq"=>""));
        return $vehicle->getData();
	}

	private function getStore(){
		return $this->storeManager->getStore();
	}
}
