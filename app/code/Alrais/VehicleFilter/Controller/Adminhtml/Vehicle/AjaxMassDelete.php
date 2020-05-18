<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Vehicle;

class AjaxMassDelete extends \Alrais\VehicleFilter\Controller\Adminhtml\Vehicle {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Product\Collection $productVehicleCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productVehicleCollection = $productVehicleCollection;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $ids = $this->getRequest()->getParam('ids');
            foreach ($ids as $id) {
                $model = $this->_objectManager->create('Alrais\VehicleFilter\Model\Product');
                $model->load($id);
                $model->delete();
            }
            $this->messageManager->addSuccess(__('You deleted the vehicles.'));
            return $result->setData(true);
        }
    }

}
