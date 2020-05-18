<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\MainMenu\Block\Adminhtml;

class Navigator extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'navigator';
        $this->_headerText = __('Menu');
        $this->_addButtonLabel = __('Add New Menu');
        parent::_construct();
    }
}
