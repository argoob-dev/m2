<?php

namespace Alrais\VehicleFilter\Controller\Model;

class ModelList extends \Magento\Framework\App\Action\Action {

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection
    ) {
        $this->vehicleCollection = $vehicleCollection;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
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
            $list = [];
            foreach($models as $model){
                $list[] = ["model_id"=> $model->getModelId(), "model_name"=> __($model->getModelName())];
            }
            return $result->setData($list);
        }
    }

}
