<?php

namespace Alrais\VehicleFilter\Controller\Vehicle;

class Get extends \Magento\Framework\App\Action\Action {

    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Alrais\VehicleFilter\Model\Resource\Make\Collection $makes,
            \Alrais\VehicleFilter\Helper\Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->makes = $makes;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $vehicleInfo = json_decode($this->helper->getVehicle());
            $makes = $this->getMakes();
            $mediaPath = $this->getMediaPath();
            if(!$vehicleInfo){
                $html = '<div class="vehicle_select"><span>'. __('SELECT YOUR VEHICLE') .'</span><i class="fa fa-angle-down"></i></div>';
            }else{
                $html= '<div class="vehicle_select"><span>' .$vehicleInfo->year." ". __($vehicleInfo->make)." "
                        . __($vehicleInfo->model) .'</span><i class="fa fa-angle-down"></i></div>';
            }
            $html .= '<div class="selectvehicle" style="display: none;"><div class="forload">'
                    . '<div class="loading-mask" data-role="loader" style="display: none;">'
                    . '<div class="loader"><img alt="Loading..." src=""><p>Please wait...</p></div></div>'
                    . '<form action="' . $this->_url->getUrl() .'vehicle_fits/vehicle/add'
                    . '" method="POST" id="vafForm" name="vafForm" class="search-sidebar">'
                    . '<input type="hidden" id="media_path" value="' . $mediaPath . '"><div>'
                    . '<select id="vehicle-fits-make" name="make_id" class="makeSelect">'
                    . '<option value="0">- '. __('Select Make') .'-</option>';
            foreach ($makes as $make){
                $html .= '<option value="' . $make->getId() .'">' . __($make->getMakeEn()) .'</option>';
            }
            $html .= '</select><select id="vehicle-fits-model" name="model_id" class="modelSelect" disabled="">'
                    . '<option value="0">- ' . __('Select Model') . ' -</option></select>';
                                    
            $html .= '<select id="vehicle-fits-year" name="year_id" class="yearSelect" disabled="">'
                    . '<option value="0">- ' . __('Select Year') . '-</option></select>';

            
            $recentvehicles = json_decode($this->helper->getRecentVehicles());        
            if($recentvehicles){
                $html .= '<div class="prev-vehicles"><label>' . __('or previously selected vehicles') . '</label>'
                    . '<select id="vehicle-fits-recent" name="vehicle_id" class="recentSelect">'
                    . '<option value="0">- '. __('Select Vehicle') .'-</option>';
            
                foreach($recentvehicles as $vehicle){
                    $html .= '<option value="'. $vehicle->vehicle_id .'">'.$vehicle->year.' '. __($vehicle->make).' '
                            . __($vehicle->model) .'</option>';
                }
                $html .= '</select></div>';
            }        
            
            if($vehicleInfo){
                $html .= '<a class="clear-fits" href="' . $this->_url->getUrl() .'vehicle_fits/vehicle/remove">'
                        . __('Clear selected vehicle') . '</a>';
            }
            $html .= '</div></form></div></div>';
            return $result->setData($html);
        }
    }

    private function getMediaPath(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    private function getMakes($productId = null) {
        return $this->makes;
    }
}
