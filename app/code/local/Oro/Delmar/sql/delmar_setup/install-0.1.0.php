<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'sync_status', 'int(10)');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'vendor_sku', 'VARCHAR(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'vendor_sku', 'VARCHAR(255)');

$installer->endSetup();
