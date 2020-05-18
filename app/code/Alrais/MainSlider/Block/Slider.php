<?php
namespace Alrais\MainSlider\Block;

use Magento\Framework\View\Element\Template;
 
class Slider extends Template
{
	protected $_sliderFactory;
        protected $_attachmentFactory;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Alrais\MainSlider\Model\SliderFactory $sliderFactory,
                \Alrais\MainSlider\Model\AttachmentFactory $attachmentFactory
	) {
		$this->_storeManager = $context->getStoreManager();
		$this->_sliderFactory = $sliderFactory;
                $this->_attachmentFactory = $attachmentFactory;
		parent::__construct($context);
	}
	public function getMainSlider()
	{
		$store_id = $this->_storeManager->getStore()->getId();
		$sliderCollection = $this->_sliderFactory->create()
			->getCollection()
			->addFieldToFilter('status', 1)
			->addFieldToFilter('store_id', array('finset'=> $store_id));
		return $sliderCollection;
	}
	public function getMediaUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
        
        public function getAttachments($sliderId)
	{
		$attachments = $this->_attachmentFactory->create()->getCollection()->addFieldToFilter('slider_id',$sliderId);
		return $attachments;
	}
        
}
