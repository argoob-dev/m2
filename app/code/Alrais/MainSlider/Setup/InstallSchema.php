<?php

namespace Alrais\MainSlider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('alrais_mainslider');
        
        if (!$installer->getConnection()->isTableExists($tableName)) {
            $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn('slider_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID')
                    ->addColumn('caption', Table::TYPE_TEXT, null, ['nullable' => false, 'default' => ''], 'Name')
                    ->addColumn('link', Table::TYPE_TEXT, null, ['nullable' => false, 'default' => ''], 'Link')
                    ->addColumn('image',Table::TYPE_TEXT, 255, [], 'Banner Image')
                    ->addColumn('sort_order', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true, 'default' => '0'], 'Sort Order')
                    ->addColumn('status', Table::TYPE_INTEGER, 0, ['default' => 1], 'Status')
                    ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
                    ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                    ->setComment('Main Slider Navigation Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('alrais_mainslider_attachment')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('alrais_mainslider_attachment'))
                ->addColumn('attachment_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ], 'Attachment ID')
                ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable => false'], 'Title')
                ->addColumn('link', Table::TYPE_TEXT, '64k', ['nullable' => false, 'default' => ''], 'Link')
                ->addColumn('image', Table::TYPE_TEXT, '64k', [], 'Image')
                ->addColumn('status', Table::TYPE_INTEGER, 0, ['default' => 1], 'Status')
                ->addColumn('slider_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Product Id')
                ->addForeignKey(
                    $installer->getFkName(
                        'alrais_mainslider',
                        'slider_id',
                        'alrais_mainslider_attachment',
                        'slider_id'
                    ),
                    'slider_id',
                    $installer->getTable('alrais_mainslider'),
                    'slider_id',
                    Table::ACTION_CASCADE
                )
                ->addColumn('sort_order', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true, 'default' => '0'], 'Sort Order')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                ->setComment('Slider Attachment Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        
        $installer->endSetup();
    }

}
