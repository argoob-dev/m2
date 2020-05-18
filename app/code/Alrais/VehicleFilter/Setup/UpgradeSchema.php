<?php

namespace Alrais\VehicleFilter\Setup;

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
            $installer->getConnection()->dropColumn($installer->getTable('alrais_vehicle_make'), 'make');
            $installer->getConnection()->addColumn(
                $installer->getTable('alrais_vehicle_make'), 'make_default', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Make Default store'
                    ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('alrais_vehicle_make'), 'make_ar', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Make Arabic'
                    ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('alrais_vehicle_make'), 'make_en', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Make English'
                    ]
            );

            $installer->getConnection()->dropColumn($installer->getTable('alrais_vehicle_model'), 'model');

            $installer->getConnection()->addColumn(
                $installer->getTable('alrais_vehicle_model'), 'model_default', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Model Default store'
                    ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('alrais_vehicle_model'), 'model_ar', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Model Arabic'
                    ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('alrais_vehicle_model'), 'model_en', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Model English'
                    ]
            );
        }
        $installer->endSetup();
    }

}
