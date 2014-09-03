<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ogrid
*/
class Amasty_Ogrid_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function hasProcessingErrors(){
        
        $collection = Mage::getModel("amogrid/order_item")->getUnmappedOrders();
        return count($collection->getItems()) > 0;
    }
    
}
?>