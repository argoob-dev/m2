<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Attachment;

class Delete extends \Alrais\MainSlider\Controller\Adminhtml\Attachment
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('attachment_id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Alrais\MainSlider\Model\Attachment');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the attachment.'));
                $this->_redirect('mainslider/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('mainslider/*/edit', ['attachment_id' => $this->getRequest()->getParam('attachment_id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('mainslider/*/');
    }
}
