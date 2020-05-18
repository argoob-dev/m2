<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Api;

interface VehicleFilterInterface
{
	/**
	*
	* @api
	* @return $this
	*/
	public function getMake();
	
	/**
	*
	* @api
	* @param int $makeId
	* @return $this
	*/
	public function getModel($makeId);
	

	/**
	*
	* @api
	* @param int $makeId
	* @param int $modelId
	* @return $this
	*/
	public function getYear($makeId, $modelId);
	
	/**
	*
	* @api
	* @param int $productId
	* @return $this
	*/
	public function getProductVehicleMake($productId);
	
	/**
	*
	* @api
	* @param int $productId
	* @param int $makeId
	* @return $this
	*/
	public function getProductVehicleModel($productId, $makeId);
	

	/**
	*
	* @api
	* @param int $productId
	* @param int $makeId
	* @param int $modelId
	* @return $this
	*/
	public function getProductVehicleYear($productId, $makeId, $modelId);

	/**
	*
	* @api
	* @param int $makeId
	* @param int $modelId
	* @param int $yearId
	* @return $this
	*/
	public function getVehicle($makeId, $modelId, $yearId);

	/**
	*
	* @api
	* @param int $productId
	* @return $this
	*/
	public function getVehicleFitsProduct($productId);
}