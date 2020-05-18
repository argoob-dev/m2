<?php

namespace Alrais\VehicleFilter\Controller\Vehicle;

class Recent extends \Magento\Framework\App\Action\Action {

    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
            \Alrais\VehicleFilter\Helper\Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $vehicles = $this->helper->getRecentVehicles();
            $vehicles = json_decode($vehicles);
            $recentvehicles = [];
            foreach($vehicles as $vehicle){
                $recentvehicles[] = ["vehicle_id"=> $vehicle->vehicle_id,"make"=> __($vehicle->make),"model"=> __($vehicle->model),"year"=> $vehicle->year,"vehicle_image"=> $vehicle->vehicle_image];
            }
            return $result->setData($recentvehicles);
        }
    }

}
