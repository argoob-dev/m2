<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\MainSlider\Block\Adminhtml\Attachment\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mainslider_attachment_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Slider Attachment'));
    }
}
