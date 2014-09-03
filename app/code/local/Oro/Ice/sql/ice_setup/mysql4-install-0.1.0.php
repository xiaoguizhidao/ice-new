<?php

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

// Temporary solution.
// @todo: reorganize attribute structure

$table = $installer->getTable('eav_attribute');
$installer->run("
update {$table} set frontend_class = 'col-label'
WHERE attribute_code LIKE '%iceus%type%'
");

$installer->run("
update {$table} set frontend_class = 'col-value'
WHERE attribute_code LIKE '%item_type%'
");

$installer->endSetup(); 
