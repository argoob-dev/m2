<?php
namespace Alrais\Cashondelivery\Plugin;

class Pickup
{

    const XML_PATH_FREE_SHIPPING_SUBTOTAL = "carriers/freeshipping/free_shipping_subtotal";

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
    }

    public function afterCollectRates(\Magento\OfflineShipping\Model\Carrier\Pickup $pickup, $result)
    {
        $scopeId = $this->_storeManager->getStore()->getId();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        $freeShippingSubTotal = $this->_scopeConfig->getValue(self::XML_PATH_FREE_SHIPPING_SUBTOTAL, $storeScope, $scopeId);

        $baseSubTotal = $this->_checkoutSession->getQuote()->getBaseSubtotal();

        if(!empty($baseSubTotal) && !empty($freeShippingSubTotal)) {

            if($baseSubTotal >= $freeShippingSubTotal) {
                return false;
            }
        }

        return $result;
    }
}