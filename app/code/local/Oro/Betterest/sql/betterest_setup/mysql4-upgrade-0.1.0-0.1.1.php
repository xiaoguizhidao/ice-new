<?php
/**
 * @category   Oro
 * @package    Oro_Betterest
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$installer->run("

        ALTER TABLE `{$this->getTable('sales_flat_order')}`
                add column custom_merchant varchar(255);
    ");


$installer->endSetup();
