<?php
/**
 * Created by PhpStorm.
 * User: igraham
 * Date: 8/17/14
 * Time: 5:19 PM
 */ 
class Oro_Orderstat_Model_Vendor extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('orderstat/vendor');
    }

    public function loadByName($name)
    {
        $this->_getResource()->loadByName($this, $name);
        return $this;
    }

    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }


}