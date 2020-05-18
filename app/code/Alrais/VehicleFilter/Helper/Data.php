<?php

namespace Alrais\VehicleFilter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Data extends AbstractHelper {

    const VEHICLE_INFO = 'VEHICLE_INFO';
    const VEHICLE_LIST = 'VEHICLE_LIST';
    const VEHICLE_INFO_COOKIE_DURATION = 3600; // One hour
    const VEHICLE_LIST_COOKIE_DURATION = 86400; // One day

    public function __construct(SessionManagerInterface $sessionManager, 
        CookieManagerInterface $cookieManager, 
        \Alrais\VehicleFilter\Model\Resource\Product\Collection $vehicleProductCollection,
        CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->_sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
        $this->vehicleProductCollection = $vehicleProductCollection;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * @param string $vehicleInfo
     * @return void
     */
    public function addVehicle($vehicleInfo) {
        $metadata = $this->getMetaData(self::VEHICLE_INFO_COOKIE_DURATION);
        $this->cookieManager
                ->setPublicCookie(self::VEHICLE_INFO, $vehicleInfo, $metadata);
    }

    /**
     * @param string $vehicleInfo
     * @return void
     */
    public function addToRecentVehicles($vehicleInfo) {
        $metadata = $this->getMetaData(self::VEHICLE_LIST_COOKIE_DURATION);
        $this->cookieManager
                ->setPublicCookie(self::VEHICLE_LIST, $vehicleInfo, $metadata);
    }

    /**
     * @return string
     */
    public function getVehicle() {
        return $this->cookieManager->getCookie(self::VEHICLE_INFO);
    }
    
    /**
     * @return void
     */
    public function removeVehicle() {
        $this->cookieManager->deleteCookie(
                self::VEHICLE_INFO, $this->cookieMetadataFactory
                        ->createCookieMetadata()
                        ->setPath($this->_sessionManager->getCookiePath())
                        ->setDomain($this->_sessionManager->getCookieDomain())
        );
    }
    /**
     * @return string
     */
    public function getRecentVehicles() {
        return $this->cookieManager->getCookie(self::VEHICLE_LIST);
    }

    private function getMetaData($cookieDuration) {
        return $this->cookieMetadataFactory
                        ->createPublicCookieMetadata()
                        ->setDuration($cookieDuration)
                        ->setPath($this->_sessionManager->getCookiePath())
                        ->setDomain($this->_sessionManager->getCookieDomain());
    }

    public function isVehicleFitsProduct($vehicleId, $productId) {
        $vehicle = $this->vehicleProductCollection
                    ->addFieldToFilter('make_model_year_id', $vehicleId)
                    ->addFieldToFilter('entity_id', $productId);
        return count($vehicle) > 0 ? true : false;

    }

    public function getVehicleFitsProduct($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $vehicle = $objectManager->create('Alrais\VehicleFilter\Model\Resource\Product\Collection')
                    ->addFieldToFilter('entity_id', $productId);
        $vehicle->getSelect()->joinLeft(
                ['vehicle' => $vehicle->getTable('alrais_vehicle_make_model_year')], 'main_table.make_model_year_id = vehicle.id', []);
        $vehicle->getSelect()->joinLeft(
                ['make' => $vehicle->getTable('alrais_vehicle_make')], 'vehicle.make_id = make.id', []);
        $vehicle->getSelect()->joinLeft(
                ['model' => $vehicle->getTable('alrais_vehicle_model')], 'vehicle.model_id = model.id', []);
        $vehicle->getSelect()->joinLeft(
                ['year' => $vehicle->getTable('alrais_vehicle_year')], 'vehicle.year_id = year.id', ['year' => 'year.year', 'make' => 'make.make_en', 'model' => 'model.model_en']);
        $vehicle->addFieldToFilter('make.make_en', array("neq"=>""));
        $vehicle->addFieldToFilter('model.model_en', array("neq"=>""));
        $vehicle->addFieldToFilter('year.year', array("neq"=>""));
        return $vehicle;

    }

    public function clearCache(){
        $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
