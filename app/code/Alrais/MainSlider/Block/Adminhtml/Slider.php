<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\MainSlider\Block\Adminhtml;

class Slider extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'slider';
        $this->_headerText = __('Slider');
        $this->_addButtonLabel = __('Add New Slider');
        parent::_construct();
    }
}
