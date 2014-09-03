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
 * Create table 'orderstat/index'
 */
$tableName = $installer->getTable('orderstat/index');

if($installer->getConnection()->isTableExists($tableName) != true){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('orderstat/index'))
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Product Id')
        ->addColumn('vendor_sku', Varien_Db_Ddl_Table::TYPE_CHAR, 255, array(
        ), 'Vendor Sku')
        ->addIndex(
            $installer->getConnection()->getIndexName($installer->getTable('orderstat/index'), 'vendor_sku'),
            'vendor_sku'
        );
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
