<?php

namespace Alrais\MainMenu\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        // Get alrais_brand table
        $tableName = $installer->getTable('alrais_mainmenu');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                            'id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                            ], 'ID'
                    )
                    ->addColumn(
                            'name', Table::TYPE_TEXT, null, ['nullable' => false, 'default' => ''], 'Name'
                    )
                    ->addColumn(
                            'menu_type', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 1], 'Menu Type'
                    )
                    ->addColumn(
                            'link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false, 'default' => ''], 'Link'
                    )
                    ->addColumn(
                            'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 1], 'Status'
                    )
                    ->setComment('Main Menu Navigation Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

}
