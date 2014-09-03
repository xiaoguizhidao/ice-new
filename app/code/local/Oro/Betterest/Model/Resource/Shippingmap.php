<?php
class Oro_Betterest_Model_Resource_Shippingmap extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init("betterest/shippingmap", "id");
    }
}