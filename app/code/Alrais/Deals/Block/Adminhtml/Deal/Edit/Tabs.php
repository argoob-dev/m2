<?php

namespace Alrais\Deals\Block\Adminhtml\Deal\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('deal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Deal Information'));
    }
}
