<?php

namespace Alrais\MainSlider\Model\System\Config;

class SliderType implements \Magento\Framework\Option\ArrayInterface {

    const WEB = 1;
    const MOBILE = 2;

    public function getOptionArray() {
        return [
            self::WEB => __('Web'),
            self::MOBILE => __('Mobile')
        ];
    }

    public function toOptionArray() {
        return [
            [
                'value' => self::WEB,
                'label' => __('Web'),
            ],
            [
                'value' => self::MOBILE,
                'label' => __('Mobile'),
            ]
        ];
    }

}