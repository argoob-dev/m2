<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainMenu\Controller\Adminhtml\Navigator;

class Index extends \Alrais\MainMenu\Controller\Adminhtml\Navigator
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
        $resultPage->setActiveMenu('Alrais_MainMenu::navigator');
        $resultPage->getConfig()->getTitle()->prepend(__('Alrais Main Menu'));
        $resultPage->addBreadcrumb(__('Main Menu'), __('Main Menu '));
        $resultPage->addBreadcrumb(__('Main Menu'), __('Main Menu'));
        return $resultPage;
    }
}
