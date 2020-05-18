<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainMenu\Controller\Adminhtml\Navigator;

class Edit extends \Alrais\MainMenu\Controller\Adminhtml\Navigator
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Alrais\MainMenu\Model\Navigator');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('alrais_mainmenu/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_alrais_mainmenu_navigator', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('navigator_navigator_edit');
        $this->_view->renderLayout();
    }
}
