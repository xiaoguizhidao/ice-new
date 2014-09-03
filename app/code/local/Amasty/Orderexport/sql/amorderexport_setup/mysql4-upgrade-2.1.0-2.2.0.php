<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD  `filter_sku_onlylines` TINYINT( 1 ) UNSIGNED NOT NULL AFTER  `filter_sku` ;
");

$installer->endSetup();