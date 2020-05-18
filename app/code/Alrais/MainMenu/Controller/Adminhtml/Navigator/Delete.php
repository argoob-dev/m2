<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainMenu\Controller\Adminhtml\Navigator;

class Delete extends \Alrais\MainMenu\Controller\Adminhtml\Navigator
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Alrais\MainMenu\Model\Navigator');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the slide.'));
                $this->_redirect('alrais_mainmenu/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('alrais_mainmenu/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('alrais_mainmenu/*/');
    }
}
