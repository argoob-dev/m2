<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\ProductWidget\Model;

use Alrais\ProductWidget\Api\ProductWidgetInterface;

class ProductWidgetApi implements ProductWidgetInterface {

    protected $_reviewFactory;
    protected $bestSellerCollection;
    protected $wishListHelper;
    protected $_pricehelper;
    protected $_imagehelper;
    protected $_storeManager;
    protected $mostViewedCollection;

    public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Framework\Stdlib\DateTime\DateTime $datetime, \Magento\Sales\Model\Order\Status\History $orderstatus, \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection, \Magento\CatalogInventory\Helper\Stock $stockHelper, \Magento\Review\Model\ReviewFactory $reviewFactory, \Magento\Review\Model\ResourceModel\Review\Summary\Collection $ratingCollection, \Magento\Catalog\Model\ProductFactory $_productloader, \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellerCollectionFactory, \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $mostViewedCollectionFactory, \Magento\Wishlist\Helper\Data $wishListHelper, \Magento\Framework\Pricing\Helper\Data $pricehelper, \Magento\Catalog\Helper\Image $imagehelper, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->_coreResource = $resource;
        $this->urlHelper = $urlHelper;
        $this->_datetime = $datetime;
        $this->_orderstatus = $orderstatus;
        $this->productCollection = $productCollection;
        $this->_pricehelper = $pricehelper;
        $this->_imagehelper = $imagehelper;
        $this->ratingCollection = $ratingCollection;
        $this->stockHelper = $stockHelper;
        $this->bestSellerCollection = $bestSellerCollectionFactory->create();
        $this->mostViewedCollection = $mostViewedCollectionFactory->create();
        $this->wishListHelper = $wishListHelper;
        $this->_reviewFactory = $reviewFactory;
        $this->_productloader = $_productloader;
        $this->localeDate = $localeDate;
    }

    public function getMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getProducts() {

        $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', '1')
                ->addAttributeToSort('created_at', 'desc');

        $_rating = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewFactory = $objectManager->get(\Magento\Review\Model\ReviewFactory::class);
        foreach ($collection as $product) {
            $reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
            $ratingSummary = $product->getRatingSummary()->getRatingSummary();
            if ($ratingSummary != null) {
                array_push($_rating, array(
                    'rating' => $ratingSummary,
                    'product' => $product->getId()
                ));
            }
        }
        usort($_rating, function($a, $b) {
            return $a['rating'] <= $b['rating'];
        });
        return array_column($_rating, 'product');
    }

    /**
     *
     * @api
     * @param int $type
     * @param int $pagefrom
     * @param int $pageto
     * @return $this
     */
    public function GetWidgetDetails($type, $pagefrom, $pageto) {
        $limit = 0;
        $widgetdetails = array();
        $now = $this->_datetime->gmtDate();
        $storeId = $this->_storeManager->getStore()->getId();
        $todayStartOfDayDate = $this->localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $widgtetype = $type;
        switch ($widgtetype) {
            case 'deals':
                $widgetcollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')
                        ->addAttributeToFilter('visibility', 4)
                        ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $now))
                        ->addAttributeToFilter('special_to_date', array('date' => true, 'from' => $now))
                        ->setPageSize($pageto)
                        ->load();
                break;
            case 'featured':

                $widgetcollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('featured_product', '1')->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'qty>0')->setPageSize($pageto)->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
                break;
            case 'bestseller':

                $widgetcollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('bestseller', '1')->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'qty>0')->setPageSize($pageto)->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
                break;
            case 'combooffer':

                $widgetcollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('combo_offer', '1')->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'qty>0')->setPageSize($pageto)->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
                break;
            case 'mostviewed':
                $widgetcollection = $this->mostViewedCollection->addAttributeToSelect('*')->addViewsCount()->setStoreId($storeId)->addStoreFilter(
                                $storeId)->setPageSize($pageto);
                break;
            case 'newarrivals':
                $widgetcollection = $this->_productCollectionFactory->create()
                                ->addAttributeToSelect('*')->addAttributeToFilter('status', '1')
                                ->addStoreFilter()->addAttributeToFilter(
                                'news_from_date', [
                            'or' => [
                                0 => ['date' => true, 'to' => $todayEndOfDayDate],
                                1 => ['is' => new \Zend_Db_Expr('null')],
                            ]
                                ], 'left'
                        )->addAttributeToFilter(
                                'news_to_date', [
                            'or' => [
                                0 => ['date' => true, 'from' => $todayStartOfDayDate],
                                1 => ['is' => new \Zend_Db_Expr('null')],
                            ]
                                ], 'left'
                        )->addAttributeToFilter(
                                [
                                    ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                                    ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
                                ]
                        )->addAttributeToSort(
                                'news_from_date', 'desc'
                        )->setPageSize($pageto);
                break;
            case 'popularitems':

                $widgetcollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('random_product', '1')->joinField('popularitems', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'qty>0')->setPageSize($pageto)->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
                break;
            case 'toprated':
                $productIds = $this->getProducts();
                $widgetcollection = $this->_productCollectionFactory->create()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('status', '1')
                                ->addAttributeToFilter('entity_id', array('in' => $productIds))->setPageSize($pageto);
            case 'attractions':
                $widgetcollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')->addAttributeToSort('created_at', 'desc')->addAttributeToFilter('is_attractions', '1')->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'qty>0')->setPageSize($pageto)->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
                break;
            default:
        }
        $widgetcount = count($widgetcollection);
        if (count($widgetcollection)) {
            foreach ($widgetcollection as $widgetprdct) {
                if ($limit >= $pagefrom) {
                    $actualprice = floatval($widgetprdct->getPrice());
                    $finalprice = floatval($widgetprdct->getFinalPrice());
                    if ($actualprice > 0 && $actualprice != $finalprice) {
                        $discount = round(($actualprice - $finalprice) * (100 / $actualprice)) . "%";
                        $actualprice = $this->_pricehelper->currency($actualprice, true, false);
                        $finalprice = $this->_pricehelper->currency($finalprice, true, false);
                    } else {
                        $discount = "0%";
                        if ($actualprice == $finalprice || $actualprice == 0) {
                            $finalprice = $this->_pricehelper->currency($finalprice, true, false);
                            $actualprice = "0";
                        }
                    }
                    $data = array(
                        "id" => $widgetprdct->getId(),
                        "sku" => $widgetprdct->getSku(),
                        "type" => $widgetprdct->getTypeId(),
                        "name" => $widgetprdct->getName(),
                        "description" => $widgetprdct->getDescription(),
                        "price" => $this->_pricehelper->currency($widgetprdct->getPrice(), true, false),
                        "specialprice" => $finalprice,
                        "discount" => $discount,
                        "url" => $widgetprdct->getProductUrl(),
                        "image" => $this->_imagehelper->init($widgetprdct, 'product_page_image_small')->resize(160, 200)->setImageFile($widgetprdct->getThumbnail())->keepAspectRatio(TRUE)->getUrl()
                    );
                    $widgetdetails[] = $data;
                }
                $limit = $limit + 1;
            }
            $total_products[] = array(
                "count" => $widgetcount,
                "products" => $widgetdetails
            );
            return $total_products;
        } else {
            $total_products[] = array(
                "count" => $widgetcount
            );
            return $total_products;
        }
    }

}
