<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderexport
*/
class Amasty_Orderexport_Model_Link extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderexport/link');
    }
    
    public function getAlias()
    {
        if ($this->getData('alias'))
        {
            return $this->getData('alias');
        }
        return $this->getData('table_name');
    }
}
