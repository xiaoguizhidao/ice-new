<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Model_Mysql4_Field_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderattach/field');
    }
}