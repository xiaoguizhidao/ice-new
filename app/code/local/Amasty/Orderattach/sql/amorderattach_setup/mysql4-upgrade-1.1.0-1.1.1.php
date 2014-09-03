<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('amorderattach/field')}` ADD `default_value` VARCHAR( 255 ) NOT NULL AFTER `type` ;
");

$installer->endSetup(); 