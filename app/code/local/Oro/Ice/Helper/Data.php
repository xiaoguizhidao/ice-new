<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Helper Class
 */
class Oro_Ice_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ATTRIBUTE_COLUMN_NUMBER = 'iceau_menu_column_number';
    const ATTRIBUTE_COLUMN_LABEL = 'iceau_menu_label_col';
    const ATTRIBUTE_VIEWMORE_LINK = 'iceau_menu_viewmore_link_text';
    const ATTRIBUTE_RIBBON_CLEARANCE = 'iceau_clearance_category';
    const ATTRIBUTE_RIBBON_NEW = 'iceau_new_category';


    protected $_ribbonConfig = null;


    /**
     * Gets Product ribbon tag
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getRibbon($product)
    {
        $productCats = $product->getCategoryIds();

        $ribbon = '';
        foreach ($this->getRibbonConfig() as $ribbonType => $ribbonValue) {
            if (is_string($ribbonValue) && method_exists($this, $ribbonValue)) {
                $ribbon = $this->$ribbonValue($product);
            } elseif (is_array($ribbonValue) && array_intersect($ribbonValue, $productCats)) {
                $ribbon = $ribbonType;
            }

            if ($ribbon) {
                break;
            }
        }

        return $ribbon;
    }

    /**
     * Checks if product should be tagged with 'sale' ribbon
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function isSaleRibbon($product)
    {
        return $product->getPrice() > $product->getFinalPrice()? 'sale': '';
    }

    /**
     * Loads Category ids for category depended ribbons and returns config array
     *
     * @return array
     */
    public function getRibbonConfig()
    {
        if ($this->_ribbonConfig === null) {
            $this->_ribbonConfig = array(
                'clearance' => array_keys(Mage::getResourceModel('catalog/category_collection')
                    ->addAttributeToFilter(self::ATTRIBUTE_RIBBON_CLEARANCE, 1)
                    ->getItems()),
                'sale' => 'isSaleRibbon',
                'new' => array_keys(Mage::getResourceModel('catalog/category_collection')
                    ->addAttributeToFilter(self::ATTRIBUTE_RIBBON_NEW, 1)
                    ->getItems())
            );
        }

        return $this->_ribbonConfig;
    }

}
