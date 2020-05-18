<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Vehicle;

class Delete extends \Alrais\VehicleFilter\Controller\Adminhtml\Vehicle
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Alrais\VehicleFilter\Model\Vehicle');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the vehicle.'));
                $this->_redirect('alrais_vehiclefilter/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('alrais_vehiclefilter/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('alrais_vehiclefilter/*/');
    }
}
