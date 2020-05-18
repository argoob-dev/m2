<?php 
namespace Alrais\CategoryListing\Model;

use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel
{
    const CATEGORY_TARGET_SELF = 0;

    const CATEGORY_TARGET_PARENT = 1;

    const CATEGORY_TARGET_BLANK = 2;

    const CACHE_TAG = 'alrais_categorylisting';

    protected $_cacheTag = 'alrais_categorylisting';

    protected $_eventPrefix = 'alrais_categorylisting';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Alrais\CategoryListing\Model\ResourceModel\Category');
    }

    public function getAvailableStatuses()
    {
        $availableOptions = array('1' => 'Enable','0' => 'Disable');
        
        return $availableOptions;
    }
    

    public function getTargetValue()
    {
        switch ($this->getTarget()) {
            case self::CATEGORY_TARGET_BLANK:
                return '_blank';
            case self::CATEGORY_TARGET_PARENT:
                return '_parent';

            default:
                return '_self';
        }
    }


}

