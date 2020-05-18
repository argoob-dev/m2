<?php

namespace Alrais\VehicleFilter\Plugin;

class Layer {

    public function __construct(
            \Magento\Framework\App\RequestInterface $request,
    \Alrais\VehicleFilter\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->helper = $helper;
    }

    public function beforePrepareProductCollection(\Magento\Catalog\Model\Layer $subject, $collection) {
        $vehicle = json_decode($this->helper->getVehicle());
        $showAll = $this->request->getParam('show_all');
        if ($vehicle && $showAll !="true") {
            $collection->getSelect()->where("make_model_year_id = " . $vehicle->vehicle_id)
                    ->join(['vehicle' => $collection->getTable('alrais_vehicle_make_model_year_entity_mapping')], 'e.entity_id = vehicle.entity_id', []);
        }
    }

}
