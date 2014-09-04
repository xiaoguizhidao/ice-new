<?php
class Ice_Pricekit_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getProducts()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        return $collection;
    }

}

