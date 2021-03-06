<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ogrid
*/
class Amasty_Ogrid_Adminhtml_SettingsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        
        $this->_addContent($this->getLayout()->createBlock('amogrid/adminhtml_settings'))
                ->_addLeft($this->getLayout()->createBlock('amogrid/adminhtml_settings_tabs'))
                ->renderLayout();
    }
    
    public function processAction()
    {
        $attributes = $this->getRequest()->getParam('attribute', array());
        $orderItem = Mage::getModel("amogrid/order_item");
        $codes = array_keys($attributes);
        $orderItem->mapData($codes, array(), TRUE);
        
        $backUrl = Mage::app()->getRequest()->getParam('backurl');
        if (!$backUrl)
        {
            $backUrl = $this->getUrl('*/adminhtml_settings/index');
        }
        
        $this->_getSession()->addSuccess($this->__('The attributes has been saved.'));
        
        $unmappedOrders = $orderItem->getUnmappedOrders();
        foreach($unmappedOrders as $unmappedOrder){
            $label = $unmappedOrder->getIncrementId()." wasn't mapped, ". $unmappedOrder->getName() . " wasn't found";
            $this->_getSession()->addNotice($label);
        }
        
        if (count($unmappedOrders) > 0){
            $orderItem->clearTemporaryData();
        }
        
        
        $this->getResponse()->setRedirect($backUrl);
    }
}
?>