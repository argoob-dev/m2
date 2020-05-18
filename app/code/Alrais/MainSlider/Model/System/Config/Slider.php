<?php

namespace Alrais\MainSlider\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;
use Alrais\MainSlider\Model\SliderFactory;

class Slider implements ArrayInterface {

    protected $_sliderFactory;

    public function __construct(
        SliderFactory $sliderFactory
    ) {
        $this->_sliderFactory = $sliderFactory;
    }
    
    public function toOptionArray() {
        $sliderCollection = $this->_sliderFactory->create()->getCollection();
        $sliderArray = [];
        foreach ($sliderCollection as $slider){
            array_push($sliderArray, ["label"=> $slider->getCaption(), "value"=> $slider->getSliderId()]);
        }
        return $sliderArray;
    }

}
