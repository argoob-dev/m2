<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\VehicleFilter\Block\Adminhtml;

class Year extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'year';
        $this->_headerText = __('Year');
        $this->_addButtonLabel = __('Add New Year');
        parent::_construct();
    }  
}
