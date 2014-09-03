<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
class Amasty_Audit_Adminhtml_LoginController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() 
    {
        $this->loadLayout(); 
        $this->_setActiveMenu('system/amaudit');
        $this->_title($this->__('Login Attemps'));
               
        $this->_addBreadcrumb($this->__('Audit Log'), $this->__('Login Attemps')); 
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_auditlog');
        $this->_addContent($block);         
         $this->renderLayout();
    }
    
    public function clearlockAction() 
	{
	   try
        {
             $lockModel = Mage::getModel('amaudit/lock');
             $collection = $lockModel->getCollection();
             foreach($collection as $item){
                 $count = $item->getCount();
                 if($count >= Mage::getStoreConfig('amaudit/general/numberFailed')){
                     $user = Mage::getModel('amaudit/lock')->load($item->getEntityId());
                     if($user){
                        $user->setData('count', 0); 
                        $user->save();      
                     }    
                 }
                 
             }
        }
        catch (Exception $e) 
        {
            $session = Mage::getSingleton('adminhtml/session');
            $session->addException(Mage::helper('adminhtml')->__('Remove failed!'));
            Mage::logException($e);
        }
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'amaudit'));
	}
}