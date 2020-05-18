<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Model;

class Edit extends \Alrais\VehicleFilter\Controller\Adminhtml\Model
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Alrais\VehicleFilter\Model\Model');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('alrais_vehiclefilter/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_alrais_vehiclefilter_model', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('alrais_vehiclefilter_model_edit');
        $this->_view->renderLayout();
    }
}
