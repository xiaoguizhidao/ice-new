<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD `filter_shipment_from` VARCHAR( 96 ) NOT NULL AFTER `filter_number_from_skip` ;
");

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD `filter_shipment_to` VARCHAR( 96 ) NOT NULL AFTER `filter_shipment_from` ;
");

$installer->endSetup();