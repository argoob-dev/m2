<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Attachment;

class Edit extends \Alrais\MainSlider\Controller\Adminhtml\Attachment
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('attachment_id');
        $model = $this->_objectManager->create('Alrais\MainSlider\Model\Attachment');

        if ($id) {
            $model->load($id);
            if (!$model->getSliderId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('mainslider/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_mainslider_attachment', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('mainslider_attachment_edit');
        $this->_view->renderLayout();
    }
}
