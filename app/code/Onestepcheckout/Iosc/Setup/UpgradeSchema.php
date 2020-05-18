<?php
/**
 * OneStepCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to One Step Checkout AS software license.
 *
 * License is available through the world-wide-web at this URL:
 * https://www.onestepcheckout.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@onestepcheckout.com so we can send you a copy immediately.
 *
 * @category   onestepcheckout
 * @package    onestepcheckout_iosc
 * @copyright  Copyright (c) 2017 OneStepCheckout  (https://www.onestepcheckout.com/)
 * @license    https://www.onestepcheckout.com/LICENSE.txt
 */
namespace Onestepcheckout\Iosc\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     *
     * @var string
     */
    private static $connectionName = 'checkout';

    /**
     *
     * {@inheritdoc}
     *
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.3', '>=')) {
            $this->addColumnIoscSubscribe($setup);
            $this->addColumnIoscRegistered($setup);
        }

        $setup->endSetup();
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addColumnIoscSubscribe(
        SchemaSetupInterface $setup
    ) {

        $connection = $setup->getConnection(self::$connectionName);
        $connection->addColumn($setup->getTable('quote', self::$connectionName), 'iosc_subscribe', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'unsigned' => true,
            'nullable' => false,
            'default' => 0,
            'comment' => 'OneStepCheckout  newsletter subscription status'
        ]);
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addColumnIoscRegistered(
        SchemaSetupInterface $setup
    ) {

            $connection = $setup->getConnection(self::$connectionName);
            $connection->addColumn($setup->getTable('quote', self::$connectionName), 'iosc_registered', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'OneStepCheckout customer registration status'
            ]);
    }
}
