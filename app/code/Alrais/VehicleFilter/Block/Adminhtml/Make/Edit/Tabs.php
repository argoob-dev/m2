<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
namespace Alrais\VehicleFilter\Block\Adminhtml\Make\Edit;

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
        $this->setId('alrais_vehiclefilter_make_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Make'));
    }
}
