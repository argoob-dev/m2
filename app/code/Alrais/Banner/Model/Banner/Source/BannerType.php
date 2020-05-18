<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alrais\Banner\Model\Banner\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class BannerType implements OptionSourceInterface {

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {

        $availableOptions = array('1' => 'Small banner', '2' => ' Big banner', '3' => 'Mobile Full Banner', '4' => 'Mobile Half Banner', '5' => 'Bottom Banner');

        $options = [];
        foreach ($availableOptions as $key => $label) {
            $options[] = [
                'label' => $label,
                'value' => $key,
            ];
        }
        return $options;
    }

}
