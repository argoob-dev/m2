<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Model;

class Model extends \Alrais\VehicleFilter\Controller\Adminhtml\Model {

    protected $uploaderFactory;
    protected $imageModel;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->vehicleCollection = $vehicleCollection;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $makeId = $this->getRequest()->getParam('make_id');
            $models = $this->vehicleCollection
                    ->addFieldToFilter('make_id', $makeId)
                    ->addFieldToSelect('model_id');
            $models->getSelect()->group('model_id');
            $models->getSelect()->joinLeft(
                    ['model' => $models->getTable('alrais_vehicle_model')], 'main_table.model_id = model.id', ['model_name'=>'model.model_en']);
            return $result->setData($models->getData());
        }
    }

}
