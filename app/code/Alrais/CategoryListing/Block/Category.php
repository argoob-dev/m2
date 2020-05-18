<?php

namespace Alrais\CategoryListing\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\ObjectManagerInterface;

class Category extends Template {

    protected $collectionFactory;
    protected $objectManager;
    protected $categoryFactory;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Alrais\CategoryListing\Model\ResourceModel\Category\CollectionFactory $collectionFactory, ObjectManagerInterface $objectManager, 
            \Magento\Catalog\Model\CategoryRepository $categoryRepository,
    \Magento\Catalog\Model\CategoryFactory $categoryFactory) {
        $this->collectionFactory = $collectionFactory;
        $this->objectManager = $objectManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context);
    }

    public function getCategories() {

        $store_id = $this->_storeManager->getStore()->getId();

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 1)->addFieldToFilter('banner_type', 0)
            ->addFieldToFilter('store_id', array('finset'=> $store_id))
            ->setOrder('sort_order', 'ASC')->setPageSize(9);
        /*
         * check for arguments,provided in block call
         */
        if ($ids_list = $this->getBannerBlockArguments()) {

            $collection->addFilter('category_list_id', ['in' => $ids_list], 'public');
        }

        return $collection;
    }

    public function getBannerBlockArguments() {

        $list = $this->getBannerList();

        $listArray = array();

        if ($list != '') {

            $listArray = explode(',', $list);
        }

        return $listArray;
    }

    public function getMediaDirectoryUrl() {

        $media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $media_dir;
    }

    public function getCategory($categoryId) {
        $category = $this->categoryFactory->create()->load($categoryId);
        return $category;
    }

    public function getProductCount($categoryId) {
        return count($this->getCategory($categoryId)->getProductCollection()->addAttributeToSelect('*'));
    }
    
    public function getStoreCategories() {
        $categories = $this->categoryFactory->create()->getCollection();
        return $categories;
    }
}
