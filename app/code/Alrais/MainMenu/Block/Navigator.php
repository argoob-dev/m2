<?php

namespace Alrais\MainMenu\Block;

use Magento\Framework\View\Element\Template;

class Navigator extends Template {

    protected $_brandsFactory;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Zend_Filter_Interface $templateProcessor,
            \Alrais\MainMenu\Model\NavigatorFactory $navigatorFactory
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_navigatorFactory = $navigatorFactory;
        $this->templateProcessor = $templateProcessor;
        parent::__construct($context);
    }

    public function getMainMenu() {
        $navigatorCollection = $this->_navigatorFactory->create()
                ->getCollection()->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_id', array('finset'=>$this->_storeManager->getStore()->getId()))
                ->setOrder('sort_order', 'ASC');
        return $navigatorCollection;
    }
    public function filterOutputHtml($string) 
    {
        return $this->templateProcessor->filter($string);
    }

    public function getMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

}
