<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Attachment;

class Index extends \Alrais\MainSlider\Controller\Adminhtml\Attachment
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
        $resultPage->setActiveMenu('Alrais_MainSlider::attachment');
        $resultPage->getConfig()->getTitle()->prepend(__('Alrais Main Slider'));
        $resultPage->addBreadcrumb(__('Main Slider'), __('Main Slider '));
        $resultPage->addBreadcrumb(__('Main Slider'), __('Main Slider'));
        return $resultPage;
    }
}
