<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\Banner\Model;

use Alrais\Banner\Api\BannerInterface;

class BannerApi implements BannerInterface {

    protected $_storeManager;
    protected $_brandsFactory;

    public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager, \Alrais\Banner\Model\ResourceModel\Banner\CollectionFactory $collectionFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
    }

    public function getMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     *
     * @api
     * @param int $storeId
     * @return $this
     */
    public function banners($storeId) {
        $banners = array();
        $collection = $this->collectionFactory->create()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('banner_type', array("in" => ['3', '4']))
                    ->addFieldToFilter('store_id', array('finset'=> $storeId))
                    ->setOrder('sort_order', 'ASC');
        if (count($collection)) {
            foreach ($collection as $banner) {
                $bannerType = '';
                if ($banner->getBannerType() == \Alrais\Banner\Model\System\Config\BannerType::MOBILE_FULL_BANNER) {
                    $bannerType = 'full';
                } else if ($banner->getBannerType() == \Alrais\Banner\Model\System\Config\BannerType::MOBILE_HALF_BANNER) {
                    $bannerType = 'half';
                }
                $data = array(
                    "id" => $banner->getId(),
                    "name" => $banner->getName(),
                    "image" => $this->getMediaUrl() . $banner->getBannerImage(),
                    "banner_type" => $bannerType,
                    "link_type" => $this->getLinkType($banner),
                    "data" => $this->getLink($banner),
                );
                $banners[] = $data;
            }
            return $banners;
        } else {
            return $banners;
        }
    }

    public function getLinkType($banner) {
        $linkType = "";
        switch($banner->getLinkType()){
            case \Alrais\Banner\Model\Config\Source\LinkType::NORMAL :
                $linkType = "normal";
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::EXTERNALLINK :
                $linkType = "external_link";
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::PRODUCT :
                $linkType = "product";
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::CATEGORY :
                $linkType = "category";
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::BRAND :
                $linkType = "brand";
                break;
        }
        return $linkType;
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
            case \Alrais\Banner\Model\Config\Source\LinkType::CATEGORY :
                $datatype = $banner->getDataType();
                $link = basename($datatype);
                break;
            case \Alrais\Banner\Model\Config\Source\LinkType::BRAND :
                $link = $banner->getBrand();
                break;
        }
        return $link;
    }

}
