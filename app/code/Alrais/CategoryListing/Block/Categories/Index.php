<?php
namespace Alrais\CategoryListing\Block\Categories;

use Magento\Framework\ObjectManagerInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $collectionFactory;
    protected $objectManager;
    protected $categoryFactory;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Alrais\CategoryListing\Model\ResourceModel\Category\CollectionFactory $collectionFactory, ObjectManagerInterface $objectManager) {
        $this->collectionFactory = $collectionFactory;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    public function getCategories() {

        $store_id = $this->_storeManager->getStore()->getId();

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 1)->addFieldToFilter('banner_type', 0)
            ->addFieldToFilter('store_id', array('finset'=> $store_id))
            ->setOrder('sort_order', 'ASC');

        if ($ids_list = $this->getBannerBlockArguments()) {

            $collection->addFilter('category_list_id', ['in' => $ids_list], 'public');
        }

        return $collection;
    }

    public function getMediaDirectoryUrl() {

        $media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $media_dir;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}