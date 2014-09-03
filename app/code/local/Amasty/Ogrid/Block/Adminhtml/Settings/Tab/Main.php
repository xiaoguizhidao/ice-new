<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ogrid
*/
class Amasty_Ogrid_Block_Adminhtml_Settings_Tab_Main extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        $this->setTemplate('amogrid/main.phtml');
    }
    
    public function getAttributes()
    {
        return Mage::getModel('amogrid/order_item')->getAttributes();
    }
    
    public function getMappedColumns()
    {
        return Mage::getModel('amogrid/order_item')->getMappedColumns();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('amogrid')->__('Attributes Configuration');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('amogrid')->__('Attributes Configuration');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }

}
?>