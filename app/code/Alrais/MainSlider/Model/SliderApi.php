<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Model;

use Alrais\MainSlider\Api\SliderInterface;

class SliderApi implements SliderInterface
{
	protected $_storeManager;
	protected $_slidesFactory;

	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Alrais\MainSlider\Model\SliderFactory $slidesFactory
	) {
		$this->_storeManager = $storeManager;
		$this->_slidesFactory = $slidesFactory;
	}
	public function getMediaUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
	/**
	*
	* @api
	* @return $this
	*/
	public function slides() {
		$slides = array();
		$slidesCollection = $this->_slidesFactory->create()
                        ->getCollection()->addFieldToFilter('slider_type', \Alrais\MainSlider\Model\System\Config\SliderType::MOBILE);
		if(count($slidesCollection)){
			foreach ($slidesCollection as $slide) {
                                $data = array(
                                        "id" => $slide->getId(),
                                        "title"=>$slide->getCaption(),
                                        "slide" => $this->getMediaUrl() . $slide->getImage(),
                                        "link_type" => $this->getLinkType($slide),
                                        "data" => $this->getLink($slide),
                                    );
				$slides[] = $data;
			}
			return $slides;
		}
		else{
			return $slides;
		}
	}
        
        public function getLinkType($banner) {
        $linkType = "";
        switch($banner->getLinkType()){
            case \Alrais\MainSlider\Model\System\Config\LinkType::NORMAL :
                $linkType = "normal";
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::EXTERNALLINK :
                $linkType = "external_link";
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::PRODUCT :
                $linkType = "product";
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::CATEGORY :
                $linkType = "category";
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::BRAND :
                $linkType = "brand";
                break;
        }
        return $linkType;
    }
    
    public function getLink($banner) {
        $link = "";
        switch($banner->getLinkType()){
            case \Alrais\MainSlider\Model\System\Config\LinkType::NORMAL :
                $link = "";
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::EXTERNALLINK :
                $link = $banner->getLink();
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::PRODUCT :
            case \Alrais\MainSlider\Model\System\Config\LinkType::CATEGORY :
                $datatype = $banner->getDataType();
                $link = basename($datatype);
                break;
            case \Alrais\MainSlider\Model\System\Config\LinkType::BRAND :
                $link = $banner->getBrand();
                break;
        }
        return $link;
    }
}