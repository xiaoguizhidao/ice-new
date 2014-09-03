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

/* $basicFields = array(
    'subtitle'          => 'Subtitle',
    'height_inches'     => 'Height (inches)',
    'length_inches'     => 'Length (inches)',
    'width_inches'      => 'Width (inches)',
    'flag'              => 'Flag',
    'flag_description'  => 'Flag Description',
    'tax_code'          => 'Tax Code',
    'warehouse_location' => 'Warehouse Location',
);

foreach ($basicFields as $code => $label) {
    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $code, array(
        'group'         => 'General',
        'frontend'      => '',
        'label'         => $label,
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
} */

/* $priceFields = array(
    'starting_price'   => 'Starting Price',
    'reserve_price'    => 'Reserve Price',
    'second_chance_offer_price' => 'Second Chance Offer Price',
);

foreach ($priceFields as $code => $label) {
    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $code, array(
        'group'         => 'Prices',
        'frontend'      => '',
        'label'         => $label,
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
} */

$installer->endSetup();