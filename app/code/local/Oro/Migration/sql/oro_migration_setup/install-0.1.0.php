<?php
/**
 * @category   Oro
 * @package    Oro_
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'oro_dataflow/schedule_import'
 */
$table = $this->getConnection()
    ->newTable($this->getTable('oro_migration/increment'))
    ->addColumn('table_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'primary' => true,
        'default' => ''
    ), 'Table Name')
    ->addColumn('increment_field', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,
    ), 'Auto Increment Column')
    ->addColumn('last_increment', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, array(
        'nullable'  => true,
    ), 'Last Auto Increment ID');
$this->getConnection()->createTable($table);

$installer->endSetup();
