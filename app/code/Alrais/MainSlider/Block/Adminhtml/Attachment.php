<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\MainSlider\Block\Adminhtml;

class Attachment extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'attachment';
        $this->_headerText = __('Attachment');
        $this->_addButtonLabel = __('Add New Attachment');
        parent::_construct();
    }
}
