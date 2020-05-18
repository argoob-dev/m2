<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\VehicleFilter\Block\Adminhtml;

class Model extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'model';
        $this->_headerText = __('Model');
        $this->_addButtonLabel = __('Add New Model');
        parent::_construct();
    }  
}
