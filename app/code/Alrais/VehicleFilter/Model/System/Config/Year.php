<?php

namespace Alrais\VehicleFilter\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Year implements ArrayInterface {
    
    public function __construct(
        \Alrais\VehicleFilter\Model\Resource\Year\Collection $year
    ) {
        $this->year = $year;
    }
    public function toOptionArray() {
        
        return $this->year->toOptionArray();
    }

}