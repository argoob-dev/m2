<?php

namespace Alrais\VehicleFilter\Plugin;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\BoolExpression;
use Magento\Framework\Search\Request\Query\Filter;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\App\ResourceConnection;

class IndexBuilder
{
/**
* @var \Magento\Framework\App\Config\ScopeConfigInterface
*/
protected $scopeConfig;

/**
* @var \Magento\Store\Model\StoreManagerInterface
*/
protected $storeManager;


public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Magento\Catalog\Helper\Category $categoryHelper,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\App\RequestInterface $request,
		\Alrais\VehicleFilter\Helper\Data $helper
	) {
		$this->storeManager = $storeManager;
		$this->_productCollectionFactory = $productCollectionFactory; 
		$this->_productVisibility = $productVisibility;
		$this->categoryHelper = $categoryHelper;
		$this->registry = $registry;
		$this->request = $request;
		$this->helper = $helper;
}

/**
* Build index query
*
* @param $subject
* @param callable $proceed
* @param RequestInterface $request
* @return Select
* @SuppressWarnings(PHPMD.UnusedFormatParameter)
*/
public function aroundBuild($subject, callable $proceed, RequestInterface $request)
{
		$select = $proceed($request);
		$storeId = $this->storeManager->getStore()->getStoreId();
		$rootCatId = $this->storeManager->getStore($storeId)->getRootCategoryId();
		$productUniqueIds = $this->getCustomCollectionQuery();
		$select->where('search_index.entity_id IN (' . join(',', $productUniqueIds) . ')');

		return $select;
}

/**
*
* @return ProductIds[]
*/
public function getCustomCollectionQuery() {

		/* get all category ids of current store */
		$websiteId = $this->storeManager->getStore()->getWebsiteId();
		$currentStoreAllCategories = $this->categoryHelper->getStoreCategories(false,true,true);

		$collection = $this->_productCollectionFactory->create();
		$collection->addAttributeToSelect(array('entity_id','sku'));
		// filter current website products
		$collection->addWebsiteFilter($websiteId);
		// set visibility filter
		$collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
		/*Vehicle Filter Here*/
		$vehicle = json_decode($this->helper->getVehicle());
		$showAll = $this->request->getParam('show_all');
		if ($vehicle && $showAll !="true") {
		    $collection->getSelect()->where("make_model_year_id = " . $vehicle->vehicle_id)
		            ->join(['vehicle' => $collection->getTable('alrais_vehicle_make_model_year_entity_mapping')], 'e.entity_id = vehicle.entity_id', []);
		}
		// /*SKU Filter Here*/
		// $sku = array('24-MB04', '24-MB03', '24-MB02');
		// $collection->addAttributeToFilter('sku', array('in' => $sku));

		$getProductAllIds = $collection->getAllIds();
		$getProductUniqueIds = array_unique($getProductAllIds);
		return $getProductUniqueIds;
		}

}
