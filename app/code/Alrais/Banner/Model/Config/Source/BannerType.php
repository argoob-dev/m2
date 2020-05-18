<?php

namespace Alrais\Banner\Model\Config\Source;

class BannerType implements \Magento\Framework\Option\ArrayInterface {

    const SMALL_BANNER = 1;
    const BIG_BANNER = 2;
    const MOBILE_FULL_BANNER = 3;
    const MOBILE_HALF_BANNER = 4;
    const BOTTOM_BANNER = 5;

    public function getOptionArray() {
        return [
            self::SMALL_BANNER => __('Small banner'),
            self::BIG_BANNER => __('Big banner'),
            self::MOBILE_FULL_BANNER => __('Mobile full banner'),
            self::MOBILE_HALF_BANNER => __('Mobile half banner'),
            self::BOTTOM_BANNER => __('Bottom banner')
        ];
    }

    public function toOptionArray() {
        return [
            [
                'value' => self::SMALL_BANNER,
                'label' => __('Small banner'),
            ],
            [
                'value' => self::BIG_BANNER,
                'label' => __('Big banner'),
            ],
            [
                'value' => self::MOBILE_FULL_BANNER,
                'label' => __('Mobile full banner'),
            ],
            [
                'value' => self::MOBILE_HALF_BANNER,
                'label' => __('Mobile half banner'),
            ],
            [
                'value' => self::BOTTOM_BANNER,
                'label' => __('Bottom banner'),
            ]
        ];
    }

}
