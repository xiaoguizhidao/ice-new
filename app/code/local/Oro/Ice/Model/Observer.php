<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Observer Class
 */
class Oro_Ice_Model_Observer
{

    /**
     * Adds frontend Category attributes to select if flat structure enabled. On catalog_category_flat_loadnodes_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCategoryAttributesToSelect(Varien_Event_Observer $observer)
    {
        /** @var Varien_Db_Select $select */
        $select = $observer->getSelect();
        $attributes = Mage::getConfig()->getNode('frontend/category/collection/attributes');

        $select->columns(array_keys($attributes->asArray()));
    }

} 
