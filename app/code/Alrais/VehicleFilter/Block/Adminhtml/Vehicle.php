<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\VehicleFilter\Block\Adminhtml;

class Vehicle extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'vehicle';
        $this->_headerText = __('Vehicle');
        $this->_addButtonLabel = __('Add New Vehicle');
        parent::_construct();
    }  
}
