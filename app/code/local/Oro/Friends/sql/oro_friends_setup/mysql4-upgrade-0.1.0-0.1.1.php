<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
update {$installer->getTable('salesrule/coupon')} set `type` = 1;
update {$installer->getTable('salesrule/rule')} set `use_auto_generation` = 1;
");

$installer->endSetup();
