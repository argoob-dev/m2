<?php
namespace Alrais\CategoryListing\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable($installer->getTable('alrais_categorylisting'))
        ->addColumn(
                    'category_list_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Category Listing Id'
                    )
        ->addColumn(
                    'link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false, 'default' => ''], 'Link'
            )
        ->addColumn('banner_image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Banner Image'
                    )
        ->addColumn('name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Name'
                    )
        ->addColumn('target',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Target'
                    )
        ->addColumn('sort_order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => '0'],
                    'Sort Order'
                    )
        ->addColumn('status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => '1'],
                    'Date'
                    )  
        ->addColumn('store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    63,
                    ['nullable' => false, 'default' => '0'],
                    'Store View Id'
                    )         
        ->addColumn('created_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Creation Time'
                    )
        ->addColumn('update_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Update Time'
                    );
        $installer->getConnection()->createTable($table);
    
        $installer->endSetup();
    }
}

