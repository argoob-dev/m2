<?php
namespace Alrais\Banner\Controller\Bottom;

class Index extends \Magento\Framework\App\Action\Action {

    public function __construct(
            \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Alrais\Banner\Helper\Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $banners = $this->helper->getBanners();
            return $result->setData($this->renderHtml($banners));
        }
    }

    private function renderHtml($banners){
        $html = '<div class="container"><div class="row">';
        foreach ($banners as $banner){
            if ($banner->getBannerType() == \Alrais\Banner\Model\System\Config\BannerType::BOTTOM_BANNER){
                $html .= '<div class="col-lg-4"><a href="' . $this->helper->getLink($banner) . '">'
                        .'<img src="' . $this->getMediaDirectoryUrl() . $banner->getBannerImage() . '">'
                        .'</a></div>';
            }
        }
        $html .= '</div></div>';
        return $html;
    }

    private function getMediaDirectoryUrl() {
        $media_dir = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
}
