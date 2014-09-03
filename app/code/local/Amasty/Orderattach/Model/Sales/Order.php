<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Model_Sales_Order extends Mage_Sales_Model_Order
{
    public function memo($field)
    {
        $orderAttachment = Mage::getModel('amorderattach/order_field');
        $orderAttachment->load($this->getId(), 'order_id');
        return $orderAttachment->getData($field);
    }
}