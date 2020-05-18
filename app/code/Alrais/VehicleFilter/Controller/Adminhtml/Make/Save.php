<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Make;

class Save extends \Alrais\VehicleFilter\Controller\Adminhtml\Make {
    
    protected $uploaderFactory;
    protected $imageModel;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, 
            \Magento\Framework\Registry $coreRegistry, 
            \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, 
            \Magento\Framework\View\Result\PageFactory $resultPageFactory           
    ) {
       parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            try {
                $model = $this->_objectManager->create('Alrais\VehicleFilter\Model\Make');
                
                $inputFilter = new \Zend_Filter_Input(
                        [], [], $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('alrais_vehiclefilter/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('alrais_vehiclefilter/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int) $this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('alrais_vehiclefilter/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('alrais_vehiclefilter/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                        __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('alrais_vehiclefilter/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('alrais_vehiclefilter/*/');
    }

}
