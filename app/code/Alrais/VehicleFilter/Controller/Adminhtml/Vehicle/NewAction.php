<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Vehicle;

class NewAction extends \Alrais\VehicleFilter\Controller\Adminhtml\Vehicle
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
