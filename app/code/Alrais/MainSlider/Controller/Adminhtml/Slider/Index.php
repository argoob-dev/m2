<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Slider;

class Index extends \Alrais\MainSlider\Controller\Adminhtml\Slider
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
        $resultPage->setActiveMenu('Alrais_MainSlider::slider');
        $resultPage->getConfig()->getTitle()->prepend(__('Alrais Main Slider'));
        $resultPage->addBreadcrumb(__('Main Slider'), __('Main Slider '));
        $resultPage->addBreadcrumb(__('Main Slider'), __('Main Slider'));
        return $resultPage;
    }
}
