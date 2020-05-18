<?php 
namespace Alrais\Banner\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper{
    public function __construct( 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Alrais\Banner\Model\ResourceModel\Banner\CollectionFactory $collectionFactory,
            \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }
    public function getBanners() {

        $store_id = $this->_storeManager->getStore()->getId();
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('store_id', array('finset'=> $store_id))
            ->addFieldToFilter('status', 1)->setOrder('sort_order', 'ASC');
        return $collection;
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
        return $this->_storeManager->getStore()->getBaseUrl(). "/brand/" . $brandId;
    }
}