<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Year;

class NewAction extends \Alrais\VehicleFilter\Controller\Adminhtml\Year
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
