<?php

namespace Alrais\MainMenu\Setup;

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
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainmenu'), 'content', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Content'
                    ]
            );
        }
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainmenu'), 'sort_order', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Sort Order'
                    ]
            );
        }
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('alrais_mainmenu'), 'store_id', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Store View Id'
                    ]
            );
        }
        $installer->endSetup();
    }

}
