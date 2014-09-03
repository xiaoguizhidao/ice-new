<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
class Amasty_Audit_Model_Mysql4_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amaudit/log');
    }
    
    public function clearEmpty() 
    {
        $this->addFieldToFilter('type', array('in' => array("", null)));
        foreach($this as $item) {
            if($item->getType() == "" || !$item->getType()) {
                try{
                    $entity = Mage::getModel('amaudit/log')->load($item->getEntityId()); 
                    $entity->delete(); 
                }
                catch(Exception $ex) {
                    Mage::logException($ex);
                }  
            }
        }    
    }
}