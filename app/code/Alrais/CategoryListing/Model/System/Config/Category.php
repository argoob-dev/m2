<?php
 
namespace Alrais\CategoryListing\Model\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
 
class Category implements ArrayInterface
{
	protected $_categoryCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

	public function getCategoriesArray()
    {
        $categoriesArray = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->addFieldToFilter('is_active', array('eq'=>'1'))
            ->load()
            ->toArray();

        $categories = array();
        foreach ($categoriesArray as $categoryId => $category) {
            if (isset($category['name']) && isset($category['level'])) {
                $categories[] = array(
                    'label' => $category['name'],
                    'level' => $category['level'],
                    'value' => $categoryId,
                );
            }
        }

        return $categories;
    }
    protected function _filterCategoriesCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
     
        $this->getCollection()->addFieldToFilter('categories', array('finset' => $value));
    }
    public function toOptionArray()
    {
        
        $options = array();
        
        
        $categoriesTreeView = $this->getCategoriesArray();
 
        foreach($categoriesTreeView as $value)
        {
            $catName    = $value['label'];
            $catId      = $value['value'];
            $catLevel    = $value['level'];
            
            $hyphen = '-';
            for($i=1; $i<$catLevel; $i++){
                $hyphen = $hyphen ."-";
            }
            
            $catName = $hyphen .$catName;
            
            $options[] = array(
               'label' => $catName,
               'value' => $catId
            );
            
            
        }
        
        return $options;
        
    }
}
