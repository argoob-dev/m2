<?php

namespace Alrais\ProductWidget\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'featured_product', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'group' => 'Product Details',
            'label' => 'Show In Featured Products',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'bestseller', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'group' => 'Product Details',
            'label' => 'Show In Bestseller',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'combo_offer', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'group' => 'Product Details',
            'label' => 'Show In Combo Offer Products',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'popularitems', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'group' => 'Product Details',
            'label' => 'Show In Popular Items',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
    }

}
