<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */ 
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'iceau_menu_column_number');
$attribute->setSourceModel('ice/source_category_column');
$attribute->save();

$installer->endSetup();
