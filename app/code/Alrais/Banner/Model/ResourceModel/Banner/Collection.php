<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alrais\Banner\Model\ResourceModel\Banner;

use Alrais\Banner\Model\ResourceModel\AbstractCollection;



class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Alrais\Banner\Model\Banner', 'Alrais\Banner\Model\ResourceModel\Banner');
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
        
    }

    
    
    
}
