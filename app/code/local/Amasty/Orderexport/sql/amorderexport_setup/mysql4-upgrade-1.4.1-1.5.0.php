<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD `filter_sku` TEXT AFTER `filter_skip_zero_price` ;
");

$installer->endSetup();