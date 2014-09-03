<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('orderstat/vendor');

if($installer->getConnection()->isTableExists($tableName) != true){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('orderstat/vendor'))
        ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Vendor_Id')
        ->addColumn('vendor_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Vendor Name')
        ->addColumn('vendor_code', Varien_Db_Ddl_Table::TYPE_VARCHAR,255, array(), 'Vendor Code');

    $installer->getConnection()->createTable($table);

    $installer->run("
    INSERT INTO {$installer->getTable('orderstat/vendor')}(vendor_code, vendor_name) VALUES('accuratime','Accuratime Corp');
    ");

    $installer->run("
    INSERT INTO {$installer->getTable('orderstat/vendor')}(vendor_code, vendor_name) VALUES('delmar','Delmar Mfg. LLC');
    ");
}

$installer->endSetup();