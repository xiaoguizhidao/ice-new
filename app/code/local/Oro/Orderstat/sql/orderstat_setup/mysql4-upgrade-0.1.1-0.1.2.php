<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('orderstat/index'), 'type_id', 'char(50)');

$installer->endSetup();
