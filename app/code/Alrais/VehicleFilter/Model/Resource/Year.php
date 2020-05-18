<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Model\Resource;

class Year extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('alrais_vehicle_year', 'id');
    }
}
