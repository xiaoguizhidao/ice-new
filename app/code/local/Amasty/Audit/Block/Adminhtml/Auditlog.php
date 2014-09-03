<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/
class Amasty_Audit_Block_Adminhtml_Auditlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_auditlog';
        $this->_blockGroup = 'amaudit';
        $this->_headerText = Mage::helper('amaudit')->__('Login Attemps');
        $this->_removeButton('add'); 
    }
}