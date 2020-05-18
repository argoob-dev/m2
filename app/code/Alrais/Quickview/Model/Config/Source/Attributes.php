<?php

namespace Alrais\Quickview\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Attributes implements ArrayInterface {
    
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributes
    ) {
        $this->attributes = $attributes;
    }
    
    public function getAttributes() {
        return $this->attributes->getCollection()
                ->addFieldToSelect(['attribute_id','frontend_label']);
    }

    public function toOptionArray() {
        
        $attributes = $this->getAttributes();
        $result = [];
        foreach ($attributes as $attribute) {

            array_push($result, [
                'value' => $attribute->getAttributeId(),
                'label' => $attribute->getFrontendLabel()
            ]);
        }
        return $result;
    }

}
