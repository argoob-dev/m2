<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainMenu\Model\Resource\Navigator;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Alrais\MainMenu\Model\Navigator', 'Alrais\MainMenu\Model\Resource\Navigator');
    }
}
