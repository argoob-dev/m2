<?php
namespace Alrais\VehicleFilter\Observer;

use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddClassToBody implements ObserverInterface
{
    /** @var PageConfig */
    protected $pageConfig;

    public function __construct(PageConfig $pageConfig, StoreManagerInterface $storeManager)
    {
        $this->pageConfig = $pageConfig;
        $this->_storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $store = $this->_storeManager->getStore();
        if ($store->getCode() != 'ar') {
            return;
        }else{
            $this->pageConfig->addBodyClass('direction-rtl');
        }

        
    }
}