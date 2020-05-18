<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alrais\CategoryListing\Model\ResourceModel\Category;

use Alrais\CategoryListing\Model\ResourceModel\AbstractCollection;



class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_list_id';

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
        $this->_init('Alrais\CategoryListing\Model\Category', 'Alrais\CategoryListing\Model\ResourceModel\Category');
        $this->_map['fields']['category_list_id'] = 'main_table.category_list_id';
        
    }

    
    
    
}
