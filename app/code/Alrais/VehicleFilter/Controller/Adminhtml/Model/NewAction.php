<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Model;

class NewAction extends \Alrais\VehicleFilter\Controller\Adminhtml\Model
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
