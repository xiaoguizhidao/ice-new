<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Model_Field extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderattach/field');
    }
    
    protected function _beforeSave()
    {
        if (!$this->getId())
        {
            Mage::register('amorderattach_add_field', true, true);
            $this->setFieldname($this->_parseFieldName($this->getCode()));
        }
        return parent::_beforeSave();
    }
    
    protected function _parseFieldName($name)
    {
        $name = preg_replace('/[^a-z0-9]/', '', $name);
        if (!$name)
        {
            $name = uniqid('f_');
        }
        return $name;
    }
    
    protected function _afterSave()
    {
        if (Mage::registry('amorderattach_add_field')) {
            $data = array(
                          'fieldname' => $this->getFieldname(),
                          'type'      => $this->getType()
                          );
            Mage::register('amorderattach_additional_data', $data);
        }
        return parent::_afterSave();
    }
    
    protected function _afterDelete()
    {
        Mage::getModel('amorderattach/order_field')->deleteField($this->getFieldname());
        parent::_afterDelete();
        Mage::helper('amorderattach')->clearCache();
        return $this;
    }
    
    public function getRenderer($type = 'backend')
    {
        if ('backend' == $type)
        {
            $blockName = 'amorderattach/adminhtml_sales_order_view_attachment_renderer_' . strtolower($this->getType());
        } else 
        {
            $blockName = 'amorderattach/sales_order_view_attachment_renderer_' . strtolower($this->getType());
        }
        $renderer  = Mage::app()->getLayout()->createBlock($blockName, 'amorderattach.renderer.' . strtolower($this->getType()));
        $renderer->setAttachmentField($this);
        return $renderer;
    }
}
