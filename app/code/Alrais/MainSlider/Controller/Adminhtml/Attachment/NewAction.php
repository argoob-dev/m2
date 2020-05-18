<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Attachment;

class NewAction extends \Alrais\MainSlider\Controller\Adminhtml\Attachment
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
