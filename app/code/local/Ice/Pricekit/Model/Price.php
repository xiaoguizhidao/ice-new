<?php
/**
 * Created by PhpStorm.
 * User: leah
 * Date: 8/26/14
 * Time: 5:08 PM
 */ 
class Ice_Pricekit_Model_Price extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('ice_pricekit/price');
        parent::_construct();
    }

}