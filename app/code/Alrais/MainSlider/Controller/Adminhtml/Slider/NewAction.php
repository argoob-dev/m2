<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Slider;

class NewAction extends \Alrais\MainSlider\Controller\Adminhtml\Slider
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
