<?php

namespace Alrais\Quickview\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $quickviewHtmlClass;
    protected $quickviewHtmlContent;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributes, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Alrais\ProductDetails\Helper\Data $productHelper
    ) {
        parent::__construct($context);
        $this->attributes = $attributes;
        $this->scopeConfig = $scopeConfig;
        $this->productHelper = $productHelper;
        $this->setDefaults();
    }

    public function getAttributes() {
        $attributeIds = $this->scopeConfig->getValue('quickview/attribute/list', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->attributes->getCollection()
                        ->addFieldToFilter('attribute_id', array('in', explode(',', $attributeIds)));
    }

    public function getOptions() {
        $options = $this->scopeConfig->getValue('quickview/options/list', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return explode(',', $options);
    }

    public function getQuickView($productCollectionObject) {
        $quickview = '<button class="' . $this->getQuickviewHtmlClass() . '" '
                . 'data-id="' . $productCollectionObject->getId() . '" '
                . 'data-producturl="' . $productCollectionObject->getProductUrl() . '" '
                . 'data-formkey="' . $this->productHelper->getFormKey() . '" '
                . 'data-discount="' . $this->productHelper->getDiscountPercentage($productCollectionObject) . '" '
                . $this->getName($productCollectionObject)
                . $this->getSku($productCollectionObject)
                . $this->getImage($productCollectionObject)
                . $this->getShortDescription($productCollectionObject)
                . $this->getOriginalPrice($productCollectionObject)
                . $this->getFinalPrice($productCollectionObject)
                . $this->getBrand($productCollectionObject)
                . $this->getAdditionalAttributes($productCollectionObject)
                . $this->getAddToCart($productCollectionObject)
                . $this->getWishlist($productCollectionObject)
                . '>' . $this->getQuickviewHtmlContent() . '</button>';
        $this->setDefaults();
        return $quickview;
    }

    private function getName($product) {
        $name = ' ';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::PRODUCT_NAME)) {
            $name = 'data-name="' . $product->getName() . '" ';
        }
        return $name;
    }

    private function getImage($product) {
        $image = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::PRODUCT_IMAGE)) {
            $defaultHeight = $this->productHelper->getImageHeight();
            $defaultWidth = $this->productHelper->getImageWidth();
            $this->productHelper->setImageRatio(500, 500);
            $image = 'data-image="' . $this->productHelper->getImage($product) . '" ';
            $this->productHelper->setImageRatio($defaultWidth, $defaultHeight);
        }
        return $image;
    }

    private function getShortDescription($product) {
        $desc = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::PRODUCT_DESCRIPTION)) {
            $desc = 'data-desc="' . $this->productHelper->escapeDoubleQuotes($product->getShortDescription()) . '" ';
        }
        return $desc;
    }

    private function getSku($product) {
        $desc = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::SKU)) {
            $desc = 'data-sku="' . $this->productHelper->escapeDoubleQuotes($product->getSku()) . '" ';
        }
        return $desc;
    }

    private function getAddToCart($product) {
        $addtocart = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::ADD_TO_CART)) {
            $this->productHelper->setAddtocartHtmlClass("action primary tocart quckvw_addcrt_btn");
            $this->productHelper->setAddtocartHtmlContent("<span class='flaticon-cart'>".__('Add to Cart')."</span>");
            $addtocart = 'data-addtocart=""';
            $this->productHelper->setDefaults();
        }
        return $addtocart;
    }

    private function getWishlist($product) {
        $wishlist = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::WISHLIST)) {
            $this->productHelper->setWishlistHtmlClass("wsh_btn");
            $this->productHelper->setWishlistHtmlContent("<span class='flaticon-love'>Add to Wishlist</span>");
            $wishlist = 'data-wishlist="' . $this->productHelper->escapeDoubleQuotes($this->productHelper->getWishlist($product)) . '" ';
            $this->productHelper->setDefaults();
        }
        return $wishlist;
    }

    private function getOriginalPrice($productCollectionObject) {
        $originalPrice = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::PRODUCT_ORIGINAL_PRICE)) {
            $originalPrice = 'data-originalprice="' . $this->productHelper->getOriginalPrice($productCollectionObject) . '" ';
        }
        return $originalPrice;
    }

    private function getFinalPrice($product) {
        $finalprice = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::PRODUCT_FINAL_PRICE)) {
            $finalprice = 'data-finalprice="' . $this->productHelper->getFinalPrice($product) . '" ';
        }
        return $finalprice;
    }

    private function getBrand($product) {
        $brand = '';
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::BRAND)) {
            $brand = 'data-brand="' . $product->getAttributeText('manufacturer') . '" ';
            $brand .= 'data-brandurl="' . $this->productHelper->getBrandUrl($product->getAttributeText('manufacturer')) . '" ';
        }
        return $brand;
    }

    private function getAdditionalAttributes($product) {
        $attributeHtml = "";
        if ($this->isOptionEnabled(\Alrais\Quickview\Model\Config\Source\Options::ADDITIONAL_ATTRIBUTES)) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeHtml .= "<li><span>" . $attribute->getFrontendLabel()
                        . "</span><span>" . $product->getAttributeText($attribute->getAttributeCode())
                        . "</span></li>";
            }
        }
        return $attributeHtml;
    }

    private function isOptionEnabled($option) {
        return in_array($option, $this->getOptions());
    }

    public function setDefaults() {
        $this->quickviewHtmlClass = 'quickview';
        $this->quickviewHtmlContent = '<i class="fa fa-search"></i>';
    }

    public function setQuickviewHtmlClass($class) {
        $this->quickviewHtmlClass = $class;
    }

    public function setQuickviewHtmlContent($content) {
        $this->quickviewHtmlContent = $content;
    }

    public function getQuickviewHtmlClass() {
        return $this->quickviewHtmlClass;
    }

    public function getQuickviewHtmlContent() {
        return $this->quickviewHtmlContent;
    }

}
