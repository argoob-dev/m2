<?php

namespace Alrais\VehicleFilter\Block\Adminhtml\Catalog\Product\Form;

class VehicleFits extends \Magento\Backend\Block\Template {

    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'catalog/product/edit/tab/vehicle_fits.phtml';

    protected $makeCollection;
    
    protected $modelCollection;
    
    protected $yearCollection;
    
    protected $vehicleCollection;
            
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(
            \Alrais\VehicleFilter\Model\Resource\Make\Collection $makeCollection,
            \Alrais\VehicleFilter\Model\Resource\Vehicle\Collection $vehicleCollection,
            \Alrais\VehicleFilter\Model\Resource\Product\Collection $productVehicleCollection,
            \Magento\Backend\Block\Template\Context $context, array $data = []
            ) {
        $this->makeCollection = $makeCollection;
        $this->vehicleCollection = $vehicleCollection;
        $this->productVehicleCollection = $productVehicleCollection;
        parent::__construct($context, $data);
    }

    
    public function getMakes() {
        return $this->makeCollection;
    }
    
    public function getVehicles($productId) {
            $vehicles = $this->productVehicleCollection
                    ->addFieldToFilter('entity_id', $productId);
            $vehicles->getSelect()->joinLeft(
                    ['entity_mapping' => $vehicles->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = entity_mapping.id', []);
            $vehicles->getSelect()->joinLeft(
                    ['make' => $vehicles->getTable('alrais_vehicle_make')], 'entity_mapping.make_id = make.id', []);
            $vehicles->getSelect()->joinLeft(
                    ['model' => $vehicles->getTable('alrais_vehicle_model')], 'entity_mapping.model_id = model.id', []);
            $vehicles->getSelect()->joinLeft(
                    ['year' => $vehicles->getTable('alrais_vehicle_year')], 'entity_mapping.year_id = year.id', ['vehicle'=>'concat(make.make_en, " >> ", model.model_en," >> ",year.year)']);
            return $vehicles;
    }
}
