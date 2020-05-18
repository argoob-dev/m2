<?php

namespace Alrais\VehicleFilter\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $alrais_vehicle_make_table = $installer->getConnection()->newTable($installer->getTable('alrais_vehicle_make'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Make Id')
                ->addColumn('make', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Make');
        $installer->getConnection()->createTable($alrais_vehicle_make_table);


        $alrais_vehicle_model_table = $installer->getConnection()->newTable($installer->getTable('alrais_vehicle_model'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Model Id')
                ->addColumn('model', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Model');
        $installer->getConnection()->createTable($alrais_vehicle_model_table);


        $alrais_vehicle_year_table = $installer->getConnection()->newTable($installer->getTable('alrais_vehicle_year'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Year Id')
                ->addColumn('year', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Year');
        $installer->getConnection()->createTable($alrais_vehicle_year_table);


        $alrais_vehicle_make_model_year_table = $installer->getConnection()->newTable($installer->getTable('alrais_vehicle_make_model_year'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Make Model Year Mapping Id')
                ->addColumn('make_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 15, ['nullable' => false, 'unsigned' => true], 'Make Id')
                ->addColumn('model_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 15, ['nullable' => false, 'unsigned' => true], 'Model Id')
                ->addColumn('year_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 15, ['nullable' => false, 'unsigned' => true], 'Year Id')
                ->addForeignKey($installer->getFkName('alrais_vehicle_make_model_year', 'make_id', 'alrais_vehicle_make', 'id'), 'make_id', $installer->getTable('alrais_vehicle_make'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
                ->addForeignKey($installer->getFkName('alrais_vehicle_make_model_year', 'model_id', 'alrais_vehicle_model', 'id'), 'model_id', $installer->getTable('alrais_vehicle_model'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
                ->addForeignKey($installer->getFkName('alrais_vehicle_make_model_year', 'year_id', 'alrais_vehicle_year', 'id'), 'year_id', $installer->getTable('alrais_vehicle_year'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE);
        $installer->getConnection()->createTable($alrais_vehicle_make_model_year_table);


        $alrais_vehicle_make_model_year_entity_mapping_table = $installer->getConnection()->newTable($installer->getTable('alrais_vehicle_make_model_year_entity_mapping'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Make Model Year Product Mapping Id')
                ->addColumn('make_model_year_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 15, ['nullable' => false, 'unsigned' => true], 'Make Model Year mapping Id')
                ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 25, ['nullable' => false, 'unsigned' => true], 'Product Id')
                ->addColumn('universal', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, ['nullable' => false], 'Universal')
                ->addColumn('related', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 15, ['nullable' => false], 'Related')
                ->addColumn('price', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, 15, ['nullable' => false], 'Price')
                ->addForeignKey($installer->getFkName('alrais_vehicle_make_model_year_entity_mapping', 'make_model_year_id', 'alrais_vehicle_make_model_year', 'id'), 'make_model_year_id', $installer->getTable('alrais_vehicle_make_model_year'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
                ->addForeignKey($installer->getFkName('alrais_vehicle_make_model_year_entity_mapping', 'entity_id', 'catalog_product_entity', 'entity_id'), 'entity_id', $installer->getTable('catalog_product_entity'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE);
        $installer->getConnection()->createTable($alrais_vehicle_make_model_year_entity_mapping_table);

        
        $installer->endSetup();
    }

}
