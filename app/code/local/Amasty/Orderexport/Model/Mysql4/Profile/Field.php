<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderexport
*/
class Amasty_Orderexport_Model_Mysql4_Profile_Field extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderexport/profile_field', 'entity_id');
    }
}