<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
class Amasty_Audit_Model_Log extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amaudit/log', 'entity_id');
    }
}