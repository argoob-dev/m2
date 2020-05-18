<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\MainMenu\Block\Adminhtml\Navigator\Edit;

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
        $this->setId('alrais_mainmenu_navigator_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Menu'));
    }
}
