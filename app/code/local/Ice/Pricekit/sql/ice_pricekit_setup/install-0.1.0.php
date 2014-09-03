<?php

$installer = $this;
$installer->startSetup();

// Create the ice_pricekit/price table
$tableName = $installer->getTable('ice_pricekit/entity');
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('identity'=>true, 'unsigned'=>true, 'nullable'=>false, 'primary'=>true), 'Entity Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'=>true, 'nullable'=>false))
        ->addColumn('cost', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'=> true, 'nullable'=>false))
        ->addColumn('price', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'=> true, 'nullable'=>false, 'default'=>'0.00'))
        ->addColumn('msrp', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'=>true, 'nullable'=>false))
        ->addColumn('special', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'=>true, 'nullable'=>true));

}