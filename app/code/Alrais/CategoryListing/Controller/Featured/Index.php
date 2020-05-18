<?php
namespace Alrais\CategoryListing\Controller\Featured;

class Index extends \Magento\Framework\App\Action\Action {

    public function __construct(
            \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Alrais\CategoryListing\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $store_id = $this->_storeManager->getStore()->getId();
            $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 1)->addFieldToFilter('banner_type', 0)
            ->addFieldToFilter('store_id', array('finset'=> $store_id))
            ->setOrder('sort_order', 'ASC')->setPageSize(9);
        
            return $result->setData($this->renderHtml($collection));
        }
    }

    private function renderHtml($categories){
        $html = '<div class="container"><div class="flex_block">'
                .'<div class="thirty cat_bg headstyle"><div> <h2>' . __('SHOP BY CATEGORY') .'</h2>'
                .'<a href="'.$this->_url->getUrl().'/categories'.'" class="btn">'. __('View All') .'</a>'
                .'</div></div><div class="seventy"><ul class="cat_list">';
        foreach ($categories as $category){
            $item = $this->_objectManager->create('\Magento\Catalog\Model\Category')
                                ->load($category->getCategoryId());
            $html .= '<li><a href="'.$item->getUrl().'"><img src="'.  $this->getMediaDirectoryUrl() .$category->getBannerImage() .'">'
                .'<span>'. $item->getName().'</span></a></li>';
        }
        
        $html .='</ul></div></div></div>';
        return $html;
    }

    private function getMediaDirectoryUrl() {
        $media_dir = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
}
