<?php

namespace Alrais\CategoryListing\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Category extends AbstractDb
{       
    protected function _construct()
    {
            $this->_init('alrais_categorylisting', 'category_list_id');
    }  
}