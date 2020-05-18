<?php

namespace Alrais\MainSlider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(
    SchemaSetupInterface $setup, ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainslider'), 'link_type', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Link Type'
                    ]
            );
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainslider'), 'data_type', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Data Type'
                    ]
            );
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainslider'), 'brand', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Brand Id'
                    ]
            );
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainslider'), 'slider_type', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Slider Type'
                    ]
            );
        }
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainslider'), 'store_id', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Store View Id'
                    ]
            );
        }
        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainslider'), 'mobile_image', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Mobile Image'
                    ]
            );
        }
        $installer->endSetup();
    }

}
