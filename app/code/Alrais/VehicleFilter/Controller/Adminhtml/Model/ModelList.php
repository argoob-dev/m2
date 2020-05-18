<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Model;

class ModelList extends \Alrais\VehicleFilter\Controller\Adminhtml\Model {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Model\Collection $modelCollection
    ) {
        $this->modelCollection = $modelCollection;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $models = $this->modelCollection
                    ->addFieldToSelect(["value" => "id", "label" => "model_en"])
                    ->addFieldToFilter("model_en", array('like' => '%' . $this->getRequest()->getParam('q') . '%'));
            return $result->setData($models->getData());
        }
    }

}
