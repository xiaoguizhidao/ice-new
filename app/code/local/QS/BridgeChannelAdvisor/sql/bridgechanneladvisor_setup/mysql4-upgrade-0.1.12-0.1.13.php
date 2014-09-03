<?php
/**
 * ChannelAdvisor System Config Direction
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */

$productTypes = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);

$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

/*
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'supplier', array(
    'group'         => 'General',
    'frontend'      => '',
    'label'         => 'Supplier',
    'input'         => 'text',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true
)); 
*/

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'seller_cost', array(
    'group'         => 'Prices',
    'frontend'      => '',
    'label'         => 'Seller Cost',
    'input'         => 'text',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->endSetup();