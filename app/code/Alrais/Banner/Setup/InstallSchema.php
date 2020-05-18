<?php

namespace Alrais\Banner\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable($installer->getTable('alrais_banner'))
                ->addColumn(
                        'banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                        ], 'Banner Id'
                )
                ->addColumn(
                        'link_type', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 1], 'Link Type'
                )
                ->addColumn(
                        'data_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Data Type'
                )
                ->addColumn(
                        'brand', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Brand Id'
                )
                ->addColumn(
                        'external_link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'External Link'
                )
                ->addColumn('banner_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Banner Image'
                )
                ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Name'
                )
                ->addColumn('banner_type', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 1], 'Banner Type'
                )
                ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true, 'default' => '0'], 'Sort Order'
                )
                ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'unsigned' => true, 'default' => '1'], 'Date'
                )
                ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 63, ['nullable' => false, 'default' => '0'], 'Store View Id'
                )
                ->addColumn('created_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Creation Time'
                )
                ->addColumn('update_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Update Time'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
