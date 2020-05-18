<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Model;

class Slider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Alrais\MainSlider\Model\Resource\Slider');
    }
}
