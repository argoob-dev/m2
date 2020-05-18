<?php
namespace Alrais\Deals\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('alrais_deals')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('alrais_deals'))
                ->addColumn(
                    'deal_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true]
                )
                ->addColumn('deal_name', Table::TYPE_TEXT, 255, ['nullable' => false])
                ->addColumn('from_date', Table::TYPE_DATETIME, null, ['nullable' => false], 'From Time')
                ->addColumn('to_date', Table::TYPE_DATETIME, null, ['nullable' => false], 'To Time')
                ->addColumn('creation_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Time')
                ->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Update Time')
                ->setComment('Sample table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('alrais_deals_product')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('alrais_deals_product'))
                ->addColumn('deal_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true])
                ->addColumn('product_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Product Id')
                ->addForeignKey(
                    $installer->getFkName(
                        'alrais_deals',
                        'deal_id',
                        'alrais_deals_product',
                        'deal_id'
                    ),
                    'deal_id',
                    $installer->getTable('alrais_deals'),
                    'deal_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'alrais_deals_product',
                        'product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Alrais Deals Product relation table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
