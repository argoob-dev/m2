<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Make;

class Index extends \Alrais\VehicleFilter\Controller\Adminhtml\Make
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Alrais_VehicleFilter::make');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Make'));
        $resultPage->addBreadcrumb(__('Alrais'), __('Alrais'));
        $resultPage->addBreadcrumb(__('Vehicle Filter'), __('Vehicle Filter'));
        $resultPage->addBreadcrumb(__('Manage Make'), __('Manage Make'));
        return $resultPage;
    }
}
