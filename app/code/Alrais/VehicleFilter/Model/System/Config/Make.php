<?php

namespace Alrais\VehicleFilter\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Make implements ArrayInterface {
    
    public function __construct(
    \Alrais\VehicleFilter\Model\Resource\Make\Collection $make
    ) {
        $this->make = $make;
    }
    public function toOptionArray() {
        
        return $this->make->toOptionArray();
    }

}