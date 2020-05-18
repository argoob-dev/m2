<?php
/**
 * OneStepCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to One Step Checkout AS software license.
 *
 * License is available through the world-wide-web at this URL:
 * https://www.onestepcheckout.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@onestepcheckout.com so we can send you a copy immediately.
 *
 * @category   onestepcheckout
 * @package    onestepcheckout_iosc
 * @copyright  Copyright (c) 2017 OneStepCheckout  (https://www.onestepcheckout.com/)
 * @license    https://www.onestepcheckout.com/LICENSE.txt
 */
namespace Onestepcheckout\Iosc\Model\Output;

class ShippingMethod implements OutputManagementInterface
{

    public function getOutputKey()
    {
        return 'shippingMethod';
    }

    public $scopeConfig = null;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     * @param \Magento\Framework\Api\SimpleDataObjectConverter $simpleDataObjectConverter
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $converter
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory
     * @param \Magento\Quote\Api\Data\CartExtensionFactory $cartExtensionFactory
     * @param \Magento\Quote\Model\ShippingFactory $shippingFactory
     * @param \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentPersister $shippingAssignmentPersister
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Onestepcheckout\Iosc\Helper\Data $helper,
        \Magento\Framework\Api\SimpleDataObjectConverter $simpleDataObjectConverter,
        \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $converter,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory,
        \Magento\Quote\Api\Data\CartExtensionFactory $cartExtensionFactory,
        \Magento\Quote\Model\ShippingFactory $shippingFactory,
        \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentPersister $shippingAssignmentPersister,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->converter = $converter;
        $this->shippingAssignmentFactory = $shippingAssignmentFactory;
        $this->shippingFactory = $shippingFactory;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->shippingAssignmentPersister = $shippingAssignmentPersister;
        $this->checkoutSession = $checkoutSession;
        $this->totalsCollector = $totalsCollector;
    }

    /**
     * {@inheritDoc}
     * @see \Onestepcheckout\Iosc\Model\Input\InputManagement::processPayload()
     */
    public function processPayload($input)
    {
        $data = [];

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        if ($quote->getId()) {
            if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            } else {
                $shippingAddress = $quote->getShippingAddress();

                if (!empty($input[$this->getOutputKey()])) {
                    $shippingMethodRequest = $input[$this->getOutputKey()];
                    $method = $shippingMethodRequest['shipping_carrier_code']
                            . '_' . $shippingMethodRequest['shipping_method_code'];
                    $quote = $this->prepareShippingAssignment($quote, $shippingAddress, $method);
                    $shippingAssignments = $quote->getExtensionAttributes()->getShippingAssignments();

                    $this->shippingAssignmentPersister->save($quote, current($shippingAssignments));
                }
                if (sizeof($shippingAddress->getAllItems()) != sizeof($quote->getAllItems())) {
                    $shippingAddress->setData('cached_items_all', $quote->getAllItems());
                }
                $shippingAddress->setCollectShippingRates("1")->collectShippingRates();

                $shippingRates = $shippingAddress->getGroupedAllShippingRates();

                foreach ($shippingRates as $carrierRates) {
                    foreach ($carrierRates as $rate) {
                        $data[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
                    }
                }
            }

            $data = $this
                ->serviceOutputProcessor
                ->convertValue($data, '\Magento\Quote\Api\Data\ShippingMethodInterface[]');
        }
        return $data;
    }

    /**
     * @param CartInterface $quote
     * @param AddressInterface $address
     * @param string $method
     * @return CartInterface
     */
    public function prepareShippingAssignment($quote, $address, $method)
    {
        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            $shippingAssignment = $this->shippingAssignmentFactory->create();
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->shippingFactory->create();
        }

        $shipping->setAddress($address);
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        return $quote->setExtensionAttributes($cartExtension);
    }
}
