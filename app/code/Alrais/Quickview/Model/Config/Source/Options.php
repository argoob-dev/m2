<?php

namespace Alrais\Quickview\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Options implements ArrayInterface {

    const PRODUCT_NAME                  = 1001;
    const PRODUCT_DESCRIPTION           = 1002;
    const PRODUCT_IMAGE                 = 1003;
    const PRODUCT_FINAL_PRICE           = 1004;
    const PRODUCT_ORIGINAL_PRICE        = 1005;
    const ADD_TO_CART                   = 1006;
    const WISHLIST                      = 1007;
    const RATING                        = 1008;
    const BRAND                         = 1009;
    const ADDITIONAL_ATTRIBUTES         = 1010;
    const SKU                           = 1011;

    public function toOptionArray() {

        $result = [
            [
                'label' => 'Product Name',
                'value' => self::PRODUCT_NAME,
            ],
            [
                'label' => 'Product Short Description',
                'value' => self::PRODUCT_DESCRIPTION,
            ],
            [
                'label' => 'Product Image',
                'value' => self::PRODUCT_IMAGE,
            ],
            [
                'label' => 'Product Final Price',
                'value' => self::PRODUCT_FINAL_PRICE,
            ],
            [
                'label' => 'Product Original Price',
                'value' => self::PRODUCT_ORIGINAL_PRICE,
            ],
            [
                'label' => 'Add To Cart',
                'value' => self::ADD_TO_CART,
            ],
            [
                'label' => 'Wishlist',
                'value' => self::WISHLIST,
            ],
            [
                'label' => 'Rating',
                'value' => self::RATING,
            ],
            [
                'label' => 'Brand',
                'value' => self::BRAND,
            ],
            [
                'label' => 'Sku',
                'value' => self::SKU,
            ],
            [
                'label' => 'Additional Attributes',
                'value' => self::ADDITIONAL_ATTRIBUTES,
            ]
        ];
        return $result;
    }

}
