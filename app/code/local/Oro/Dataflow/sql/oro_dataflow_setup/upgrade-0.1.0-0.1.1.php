<?php
/** @var Mage_Catalog_Model_Resource_Setup $this */
$this->startSetup();
$this->getConnection()->modifyColumn($this->getTable('oro_dataflow/schedule_import'), 'log', array(
    'type'   => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '512m'
));
$this->getConnection()->modifyColumn($this->getTable('oro_dataflow/schedule_export'), 'log', array(
    'type'   => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '512m'
));
$this->endSetup();
