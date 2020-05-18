<?php

namespace Alrais\VehicleFilter\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Model implements ArrayInterface {
    
    public function __construct(
    \Alrais\VehicleFilter\Model\Resource\Model\Collection $model
    ) {
        $this->model = $model;
    }
    public function toOptionArray() {
        
        return $this->model->toOptionArray();
    }

}