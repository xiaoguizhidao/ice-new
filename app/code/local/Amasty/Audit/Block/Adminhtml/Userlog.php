<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
class Amasty_Audit_Block_Adminhtml_Userlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_userlog';
        $this->_blockGroup = 'amaudit';
        $this->_headerText = Mage::helper('amaudit')->__('Action Log');
        $this->_removeButton('add'); 
    }
}