<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/
class Amasty_Audit_Block_Adminhtml_Userlog_Edit extends Mage_Adminhtml_Block_Widget_View_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = Mage::helper('amaudit')->__('Action Log');
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_removeButton('edit');
    }
}


