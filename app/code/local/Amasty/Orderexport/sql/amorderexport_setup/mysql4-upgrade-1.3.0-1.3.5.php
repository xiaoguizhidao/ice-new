<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD `post_date_format` VARCHAR( 32 ) NOT NULL ;
");

$installer->endSetup();