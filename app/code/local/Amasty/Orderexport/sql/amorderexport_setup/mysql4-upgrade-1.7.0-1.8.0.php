<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderexport/profile')}` ADD `post_status` VARCHAR( 196 ) NOT NULL ;
");

$installer->endSetup();