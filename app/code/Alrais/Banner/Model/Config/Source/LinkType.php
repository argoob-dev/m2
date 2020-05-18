<?php

namespace Alrais\Banner\Model\Config\Source;

class LinkType implements \Magento\Framework\Option\ArrayInterface {

    const NORMAL = 1;
    const EXTERNALLINK = 2;
    const PRODUCT = 3;
    const CATEGORY = 4;
    const BRAND = 5;

    public function getOptionArray() {
        return [
            self::NORMAL => __('Default'),
            self::EXTERNALLINK => __('External Link'),
            self::PRODUCT => __('Product'),
            self::CATEGORY => __('Category'),
            self::BRAND => __('Brand')
        ];
    }

    public function toOptionArray() {
        return [
            [
                'value' => self::NORMAL,
                'label' => __('Default'),
            ],
            [
                'value' => self::EXTERNALLINK,
                'label' => __('External Link'),
            ],
            [
                'value' => self::PRODUCT,
                'label' => __('Product'),
            ],
            [
                'value' => self::CATEGORY,
                'label' => __('Category'),
            ],
            [
                'value' => self::BRAND,
                'label' => __('Brand'),
            ]
        ];
    }

}
