<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainMenu\Controller\Adminhtml\Navigator;

class NewAction extends \Alrais\MainMenu\Controller\Adminhtml\Navigator
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
