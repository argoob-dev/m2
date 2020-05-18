<?php

namespace Alrais\Banner\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\ObjectManagerInterface;

class Banner extends Template {

    protected $collectionFactory;
    protected $objectManager;
    protected $bannerFactory;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Alrais\Banner\Model\ResourceModel\Banner\CollectionFactory $collectionFactory, ObjectManagerInterface $objectManager) {
        $this->collectionFactory = $collectionFactory;
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    public function getBanners() {
        $store_id = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('store_id', array('finset'=> $store_id))
            ->addFieldToFilter('status', 1)->setOrder('sort_order', 'ASC');
        return $collection;
    }

    public function getMediaDirectoryUrl() {

        $media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $media_dir;
    }

    public function getLink($banner) {
        $link = "";
        switch($banner->getLinkType()){
            case \Alrais\Banner\Model\Config\Source\LinkType::NORMAL :
                $link = "";
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::EXTERNALLINK :
                $link = $banner->getExternalLink();
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::PRODUCT :
                $datatype = $banner->getDataType();
                $productId = basename($datatype);
                $link = $this->getProductLink($productId);
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::CATEGORY :
                $datatype = $banner->getDataType();
                $categoryId = basename($datatype);
                $link = $this->getCategoryLink($categoryId);
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::BRAND :
                $brandId = $banner->getBrand();
                $link = $this->getBrandLink($brandId);
                break;
        }
        return $link;
    }
    
    public function getProductLink($productId) {
        $product = $this->objectManager->create("Magento\Catalog\Model\Product")
                ->load($productId);
        return $product->getProductUrl();
    }
    
    public function getCategoryLink($categoryId) {
        $category = $this->objectManager->create("Magento\Catalog\Model\Category")
                ->load($categoryId);
        return $category->getUrl();
    }
    
    public function getBrandLink($brandId) {
        return $this->getBaseUrl(). "brandlisting/brand/view?id=" . $brandId;
    }
}
