<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/   
class Amasty_Audit_Helper_Data extends Mage_Core_Helper_Url
{
    public function getNameFromArray($name) {
        $nameArray = array(
           'Mage_Customer_Model_Attribute'              =>  $this->__('Amasty Customer Attribute'),
           'Mage_Sales_Model_Quote_Address'             =>  $this->__('Order'),
           'Mage_Catalog_Model_Product'                 =>  $this->__('Product'),
           'Mage_Catalog_Model_Resource_Eav_Attribute'  =>  $this->__('Product Attribute'),
           'Mage_Eav_Model_Entity_Attribute'            =>  $this->__('Product Attribute Set'),
           'Mage_Eav_Model_Entity_Attribute_Set'        =>  $this->__('Product Attribute Set'),
           'Mage_Tax_Model_Calculation_Rule'            =>  $this->__('Tax Rules'),
           'Mage_Tag_Model_Tag'                         =>  $this->__('Tags'),
           'Find_Feed_Model_Codes'                      =>  $this->__('Attributes map'),
           'Mage_Customer_Model_Group'                  =>  $this->__('Customer Groups'),
           'Mage_CatalogRule_Model_Rule'                =>  $this->__('Catalog Price Rules'),
           'Mage_SalesRule_Model_Coupon'                =>  $this->__('Shopping Cart Price Rules'),
           'Mage_SalesRule_Model_Rule'                  =>  $this->__('Shopping Cart Price Rules'),
           'Mage_Newsletter_Model_Template'             =>  $this->__('Newsletter Templates'),
           'Mage_Cms_Model_Page'                        =>  $this->__('CMS Manage Pages'),
           'Mage_Cms_Model_Block'                       =>  $this->__('CMS Static Blocks'),
           'Mage_Widget_Model_Widget_Instance'          =>  $this->__('CMS Widget Instances'),
           'Mage_Poll_Model_Poll'                       =>  $this->__('CMS Poll'),
           'Amasty_Customerattr_Model_Rewrite_Customer' =>  $this->__('Customer'),
           'Mage_Core_Model_Config_Data'                =>  $this->__('System Configuration'),
           'Mage_Admin_Model_User'                      =>  $this->__('User'),
           'Mage_Admin_Model_Roles'                     =>  $this->__('Role'),
           'Mage_Core_Model_Design'                     =>  $this->__('System Design'),
           'Mage_Api_Model_User'                        =>  $this->__('System Web Services Users'),
           'Mage_Api_Model_Roles'                       =>  $this->__('System Web Services Roles'),
           'Mage_Adminhtml_Model_Email_Template'        =>  $this->__('System Transactional Emails'),
           'Mage_Core_Model_Variable'                   =>  $this->__('System Custom Variable'),
           'Mage_Sales_Model_Order_Item'                =>  $this->__('Order'),
           'Mage_Customer_Model_Customer'               =>  $this->__('Customer')
       );
       
       if(array_key_exists($name, $nameArray)) {
           $name = $nameArray[$name];
       } 
         
       return $name;
    }
    
    public function getLockUser($idUser)  
    {
        try
        {
             $lockModel = Mage::getModel('amaudit/lock');
             $collection = $lockModel->getCollection();
             foreach($collection as $item){
                 if($idUser == $item->getUserId()){
                     $user = Mage::getModel('amaudit/lock')->load($item->getEntityId());
                     if($user){
                        return $user;
                     }
                     break;
                 }
             }
        }
        catch (Exception $e) 
        {
             Mage::logException($e);
        }
        
        return null;
    }
    
    public function isUserInLog($userId)
    {
        $massId = Mage::getStoreConfig('amaudit/general/log_users');
        $massId = explode(',', $massId);
        if(in_array($userId, $massId)) {
            return true;    
        }
        else {
            return false;    
        }
    }
    
    public function getCacheParams($params)
    {
        $option = array();
        $cacheTypes = Mage::app()->getCacheInstance()->getTypes();
        foreach($params as $key => $value) {
            if(array_key_exists($value, $cacheTypes)){
                $option[$cacheTypes[$value]->getData('cache_type')] =  $cacheTypes[$value]->getData('description');    
            }
        }
        return $option;
    }
    
    public function getIndexParams($params)
    {
        $option = array();
        $collection = Mage::getResourceModel('index/process_collection');
        foreach($collection as $item) {
            if(in_array($item->getProcessId(), $params)){
                $option[$item->getIndexer()->getName()] =  $item->getIndexer()->getDescription();    
            }
        }
        return $option;
    }
}
