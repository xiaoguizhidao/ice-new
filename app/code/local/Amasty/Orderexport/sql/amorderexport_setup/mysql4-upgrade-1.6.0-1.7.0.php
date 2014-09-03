<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD `filter_number_to` VARCHAR( 96 ) NOT NULL AFTER `filter_number_from` ;
");

$installer->endSetup();