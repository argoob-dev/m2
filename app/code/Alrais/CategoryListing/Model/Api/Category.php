<?php

/**
 * Copyright © 2018 Alrais. All rights reserved.
 */

namespace Alrais\CategoryListing\Model\Api;

use Alrais\CategoryListing\Api\CategoryListingInterface;

class Category implements CategoryListingInterface {

    protected $_storeManager;
    protected $_brandsFactory;
    protected $_categoryHelper;

    public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Helper\Category $categoryHelper, \Alrais\CategoryListing\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
    }

    public function getMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     *
     * @api
     * @return $this
     */
    public function categories() {
        $banners = array();
        $collection = $this->collectionFactory->create()
                        ->addFieldToFilter('banner_type', 1)->addFieldToFilter('status', 1)->setOrder('sort_order', 'ASC');
        if (count($collection) > 0) {
            foreach ($collection as $banner) {
                $data = array(
                    "id" => $banner->getCategoryId(),
                    "name" => $banner->getName(),
                    "image" => $this->getMediaUrl() . $banner->getBannerImage()
                );
                $banners[] = $data;
            }
            return $banners;
        } else {
            return $banners;
        }
    }
    
    /**
     *
     * @api
     * @return $this
     */
    public function getStoreCategories() {
        $categoryList = $this->getCategoryCollection(3, null);
       	$categories = [];
        foreach($categoryList as $category){
	    $subcategoryList = $this->getCategoryCollection(4, $category->getId());
	    $subcategories = [];
	    foreach($subcategoryList as $subcategory){
		$subcategoryDetails = [
			"id" => $subcategory->getId(),
			"parent_id" => $subcategory->getParentId(),
			"name" => $subcategory->getName(),
			"position" => $subcategory->getPosition(),
			"level" => $subcategory->getLevel(),
			"is_active" => $subcategory->getIsActive(),
			"product_count" => $subcategory->getProductCount(),
			"image" => $subcategory->getMobileImage()
			
		    ];
		    array_push($subcategories, $subcategoryDetails);
	    }	
	    $categoryDetails = [
		"id" => $category->getId(),
		"parent_id" => $category->getParentId(),
		"name" => $category->getName(),
		"position" => $category->getPosition(),
		"level" => $category->getLevel(),
		"is_active" => $category->getIsActive(),
		"product_count" => $category->getProductCount(),
		"image" => $category->getMobileImage(),
		"children" => $subcategories
		
	    ];
	    array_push($categories, $categoryDetails);
        }
        return $categories;
    }

    public function getCategoryCollection($level = null, $parent = null) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categoryList = $categoryCollection->create();
	$categoryList->addAttributeToSelect('*');
	if($level){
		$categoryList->addAttributeToFilter('level', $level);
      	}
	if($parent){
		$categoryList->addAttributeToFilter('parent_id', $parent);
      	}
        return $categoryList;
    }
}
