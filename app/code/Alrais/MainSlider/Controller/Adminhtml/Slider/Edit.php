<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Slider;

class Edit extends \Alrais\MainSlider\Controller\Adminhtml\Slider
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('slider_id');
        $model = $this->_objectManager->create('Alrais\MainSlider\Model\Slider');

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
        $this->_coreRegistry->register('current_mainslider_slider', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('mainslider_slider_edit');
        $this->_view->renderLayout();
    }
}
