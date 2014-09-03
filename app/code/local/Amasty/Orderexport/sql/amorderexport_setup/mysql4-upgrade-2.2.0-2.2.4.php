<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
     ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD  `run_after_order_creation` TINYINT( 1 ) UNSIGNED NOT NULL; 
");

$installer->endSetup();