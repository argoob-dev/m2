<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Model\Resource\Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Alrais\VehicleFilter\Model\Model', 'Alrais\VehicleFilter\Model\Resource\Model');
    }
    
    public function toOptionArray()
    {
        return parent::_toOptionArray('id', 'model_en');
    }
}
