<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'delmar/index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('delmar/index'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Product Id')
    ->addColumn('vendor_sku', Varien_Db_Ddl_Table::TYPE_CHAR, 255, array(
    ), 'Vendor Sku')
    ->addIndex(
        $installer->getConnection()->getIndexName($installer->getTable('delmar/index'), 'vendor_sku'),
        'vendor_sku'
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();
