<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Make;

class NewAction extends \Alrais\VehicleFilter\Controller\Adminhtml\Make
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
