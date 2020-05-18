<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\VehicleFilter\Block\Adminhtml;

class Make extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'make';
        $this->_headerText = __('Make');
        $this->_addButtonLabel = __('Add New Make');
        parent::_construct();
    }  
}
