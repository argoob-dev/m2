<?php

namespace Alrais\ProductDetails\Helper;

use Magento\Framework\App\Action\Action;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $imageWidth;
    protected $imageHeight;
    protected $wishlistHtmlClass;
    protected $wishlistHtmlContent;
    protected $addtocartHtmlClass;
    protected $addtocartHtmlContent;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, 
    \Magento\Review\Model\ReviewFactory $reviewFactory, 
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
    \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, 
    \Magento\Framework\Data\Form\FormKey $formkey, 
    \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, 
    \Magento\Catalog\Model\Product $product, 
    \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $category, 
    \Magento\Catalog\Helper\Image $imageHelper, 
    \Magento\Customer\Model\Session $customerSession, 
    \Magento\Framework\Pricing\Helper\Data $priceHelper, 
    \Magento\Store\Model\StoreManagerInterface $storeManager, 
    \Magento\Wishlist\Helper\Data $wishlistHelper, 
    \Magento\Catalog\Block\Product\ListProduct $addtocart,
    \Magento\Catalog\Helper\Data $taxHelper, 
    \Magento\Catalog\Block\Product\ListProduct $listProduct ) {
        parent::__construct($context);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->formkey = $formkey;
        $this->localeDate = $localeDate;
        $this->product = $product;
        $this->category = $category;
        $this->imageHelper = $imageHelper;
        $this->addtocart = $addtocart;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->listProduct = $listProduct;
        $this->reviewFactory = $reviewFactory;
        $this->taxHelper = $taxHelper;
        $this->setDefaults();
    }

    public function loadProducts($collection) {
        if ($collection) {
            foreach ($collection as $value) {
                $product = $this->product->load($value->getId());
                $value->imageUrl = $this->getImage($value);
                $value->name = $value->getName();
                $value->addtocart = $this->getAddToCart($product);
                $value->wishlist = $this->getWishlist($product);
                $value->originalPrice = $this->getOriginalPrice($value);
                $value->finalPrice = $this->getFinalPrice($value);
                if ($value->getPrice() == $value->getFinalPrice()) {
                    $value->originalPrice = "";
                }
                $value->productUrl = $value->getProductUrl();
                $value->brand = $product->getAttributeText('manufacturer');
                $value->brandId = $product->getManufacturer();
                $value->brandUrl = $this->getBrandUrl($product->getManufacturer());
                $value->reviewsCount = $this->getReviewCount($value);
                $value->ratingPercentage = $this->getRatingPercentage($value);
                $value->discountPercentage = $this->getDiscountPercentage($value);
            }
        }
        return $collection;
    }

    public function setImageRatio($width, $height) {
        $this->imageWidth = $width;
        $this->imageHeight = $height;
    }

    public function getImage($product) {
        return $this->imageHelper
                        ->init($product, 'product_page_image_small')
                        ->resize($this->imageWidth, $this->imageHeight)->setImageFile($product->getThumbnail())
                        ->keepAspectRatio(TRUE)->getUrl();
    }

    public function getOriginalPrice($product) {
        // return $this->priceHelper->currency($product->getPrice(), true, false);
        return $this->priceHelper->currency($this->taxHelper->getTaxPrice($product, $product->getPrice(), true), true, false);
    }

    public function getDiscountPercentage($product) {
        $discount = 0;
        if ($product->getPrice() > 0) {
            $discount = round((($product->getPrice() - $product->getFinalPrice()) * 100) / $product->getPrice()) . '%';
        }
        return $discount;
    }

    public function getFinalPrice($product) {
        // return $this->priceHelper->currency($product->getFinalPrice(), true, false);
        // return $this->priceHelper->currency($product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(), true, false);
        // return $this->priceHelper->currency($product->getPrice(), true, false);
        // return $product->getPrice();
        // return number_format($product->getPrice(), 0, ".", ","). " درهم ";

        // $store = $this->storeManager->getStore()->getId();
        // $price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();

        // if($store == 4) {
        //     return number_format($price, 2, ".", ",") ." درهم  ";
        // } else {
        //     return " AED ". number_format($price, 2, ".", ",");
        // }
        // return $this->priceHelper->currency(number_format($product->getPrice(), 0, ".", ","), true, false);
        return $this->priceHelper->currency($this->taxHelper->getTaxPrice($product, $product->getFinalPrice(), true), true, false);
        // return $this->taxHelper->getTaxPrice($product, $product->getFinalPrice(), true);
    }


    public function getAddToCart($product) {
        $postParams = $this->getAddToCartPostParams($product);
        if ($product->isSalable()) {
            $addToCart = '<button type="submit" title="'.__('Add to Cart').'" onclick="document.location.href=\''.$product->getProductUrl().'\'" class="'. $this->getAddtocartHtmlClass() .'">'
            . $this->getAddtocartHtmlContent(). '</button>';
        } else {
            $addToCart = '<button type="submit" title="'.__('Add to Cart').'" class="'. $this->getAddtocartHtmlClass() .'">'
                    . $this->getAddtocartHtmlContent(). '</button>';
        }
        return $addToCart;
    }
    public function setDefaults() {
        $this->imageWidth = 350;
        $this->imageHeight = 350;
        $this->wishlistHtmlClass = 'action towishlist';
        $this->wishlistHtmlContent = '<i class="fa fa-heart"></i>';
        $this->addtocartHtmlClass = 'action cart_btn tocart addtocart';
        $this->addtocartHtmlContent = '<span><i class="fa fa-shopping-cart"></i></span>';
    }
    
    public function setWishlistHtmlClass($class){
        $this->wishlistHtmlClass = $class;
    }
    
    public function setWishlistHtmlContent($content){
        $this->wishlistHtmlContent = $content;
    }
    
    public function setAddtocartHtmlClass($class){
        $this->addtocartHtmlClass = $class;
    }
    
    public function setAddtocartHtmlContent($content){
        $this->addtocartHtmlContent = $content;
    }
    
    public function getWishlistHtmlClass(){
        return $this->wishlistHtmlClass;
    }
    
    public function getWishlistHtmlContent(){
        return $this->wishlistHtmlContent;
    }
    
    public function getAddtocartHtmlClass(){
        return $this->addtocartHtmlClass;
    }
    
    public function getAddtocartHtmlContent(){
        return $this->addtocartHtmlContent;
    }

    public function getWishlist($product) {
        $wishlistSubmitParams = $this->getWishlistParams($product);
        if ($this->customerSession->isLoggedIn()) {
            $addToWishlist = '<button title="Add to Wish List" class="'. $this->getWishlistHtmlClass()
                    .'" data-post=' . $wishlistSubmitParams . '>'
                    .$this->getWishlistHtmlContent()
                    . '</button>';
        } else {
            $addToWishlist = '<button class="' .$this->getWishlistHtmlClass()." show2"
                    . '">' . $this->getWishlistHtmlContent() . '</button>';
        }
        return $addToWishlist;
    }

    public function getProductById($id) {
        return $this->product->load($id);
    }

    public function getWishlistParams($product) {
        return $this->wishlistHelper->getAddParams($product);
    }

    public function getAddToCartPostParams($product) {
        return $this->listProduct->getAddToCartPostParams($product);
    }

    public function getRatingPercentage($product) {
        $rating = $this->getRatingSummary($product)->getRatingSummary();
        return $rating ? $rating : 0;
    }

    public function getReviewCount($product) {
        $rating = $this->getRatingSummary($product)->getRatingCount();
        return $rating ? $rating : 0;
    }

    public function getRatingSummary($product) {
        $this->reviewFactory->create()->getEntitySummary($product, $this->storeManager->getStore()->getId());
        $ratingSummary = $product->getRatingSummary();
        return $ratingSummary;
    }

    public function escapeDoubleQuotes($string) {
        return str_ireplace('"', '\'', $string);
    }

    public function getFormKey() {
        return $this->formkey->getFormKey();
    }

    public function getBrandUrl($brandId) {
        return $this->getBaseUrl() . "brand/" . $brandId;
    }

    public function getImageHeight() {
        return $this->imageHeight;
    }

    public function getImageWidth() {
        return $this->imageWidth;
    }

    public function getBaseUrl() {
        return $this->storeManager->getStore()->getBaseUrl();
    }

}
