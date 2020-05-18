<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Vehicle;

class AjaxProductVehicleSave extends \Alrais\VehicleFilter\Controller\Adminhtml\Vehicle {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            if ($this->getRequest()->getPostValue()) {
                $data = $this->getRequest()->getPostValue();
                try {
                    $model = $this->_objectManager->create('Alrais\VehicleFilter\Model\Product');

                    $inputFilter = new \Zend_Filter_Input(
                            [], [], $data
                    );
                    $vehicle = $this->_objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection')
                            ->addFieldToFilter('make_model_year_id', $data["make_model_year_id"])
                            ->addFieldToFilter('entity_id',  $data["entity_id"]);
                    if ($vehicle->getFirstItem()->getId()) {
                        return $result->setData(["message"=>"Vehicle already maped."]);
                    }
                    $data = $inputFilter->getUnescaped();
                    $model->setData($data);
                    $model->save();
                    $this->messageManager->addSuccess(__('You saved the item.'));
                    return $result->setData($model->getData());
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                    return $result->setData(false);
                } catch (\Exception $e) {
                    $this->messageManager->addError(
                            __('Something went wrong while saving the item data. Please review the error log.')
                    );
                    $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                    return $result->setData(false);
                }
            }
        }
    }

}
