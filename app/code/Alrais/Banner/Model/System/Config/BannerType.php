<?php

namespace Alrais\Banner\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class BannerType implements ArrayInterface {

    const SMALL_BANNER = 1;
    const BIG_BANNER = 2;
    const MOBILE_FULL_BANNER = 3;
    const MOBILE_HALF_BANNER = 4;
    const BOTTOM_BANNER = 5;

    /**
     * @return array
     */
    public function toOptionArray() {
        $options = [
            self::SMALL_BANNER => __('Small banner'),
            self::BIG_BANNER => __('Big banner'),
            self::MOBILE_FULL_BANNER => __('Mobile full banner'),
            self::MOBILE_HALF_BANNER => __('Mobile half banner'),
            self::BOTTOM_BANNER => __('Bottom banner')
        ];

        return $options;
    }

}
