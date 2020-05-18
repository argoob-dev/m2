<?php
namespace Alrais\Banner\Controller\Top;

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
        $html = '<div class="container"><div class="flex_block"><div class="small_bnr dark"><div class="bannerlabel">'
            .'<h2>' . __('SPECIAL OFFERS') . '</h2><a href="' . $this->_url->getUrl('deals') .'" class="btn">' . __('view all offers').'</a>'
            .'</div></div>';
        foreach ($banners as $banner){
            if ($banner->getBannerType() == \Alrais\Banner\Model\System\Config\BannerType::SMALL_BANNER){
                $html .= '<div class="small_bnr"><div class="banners"><a href="' . $this->helper->getLink($banner) . '">'
                    .'<img src="' . $this->getMediaDirectoryUrl() . $banner->getBannerImage() . '">'
                    .'</a></div></div>';
            }
        }
        $html .= '</div><div class="flex_block">';
        foreach ($banners as $banner){
            if ($banner->getBannerType() == \Alrais\Banner\Model\System\Config\BannerType::BIG_BANNER){
                $html .= '<div class="big_bnr"><div class="banners"><a href="' . $this->helper->getLink($banner) . '">'
                    .'<img src="' . $this->getMediaDirectoryUrl() . $banner->getBannerImage() . '">'
                    .'</a></div></div>';
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
