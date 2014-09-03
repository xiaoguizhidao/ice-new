<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Model_Observer
{
    protected $_attachments = null;
    protected $_permissibleActions = array('index', 'grid', 'exportCsv', 'exportExcel');
    protected $_exportActions = array('exportCsv', 'exportExcel');
    protected $_controllerNames = array('sales_', 'orderspro_');
    protected $_orderCollectionClasses = array('Mage_Sales_Model_Resource_Order_Grid_Collection',
                                               'Mage_Sales_Model_Resource_Order_Collection');
    protected $_orderViewClasses = array('Mage_Adminhtml_Block_Sales_Order_View_Tab_Info');
    protected $_orderGridClasses = array('Mage_Adminhtml_Block_Sales_Order_Grid',
                                         'EM_DeleteOrder_Block_Adminhtml_Sales_Order_Grid',
                                         'MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid',
                                         'Excellence_Salesgrid_Block_Adminhtml_Sales_Order_Grid',
                                         'AW_Ordertags_Block_Adminhtml_Sales_Order_Grid');
    
    public function onSalesOrderPlaceAfter($observer)
    {
        $order = $observer->getOrder();
        
        $collection = Mage::getModel('amorderattach/field')->getCollection();
        $collection->addFieldToFilter('default_value', array("neq" =>'' ));
        
        $orderField = Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
        if ($orderField->getId())
        {
            return ;
        }
        
        if ($collection->getSize() > 0)
        {
            $orderField->setOrderId($order->getId());
            foreach ($collection as $field)
            {
                $orderField->setData($field->getFieldname(), $field->getDefaultValue());
            }
            $orderField->save();
        }
    }
    
    public function onModelSaveCommitAfter($observer)
    {
        $object = $observer->getObject();
        
        if (($object instanceof Amasty_Orderattach_Model_Field) && (Mage::registry('amorderattach_additional_data'))) {
            $data = Mage::registry('amorderattach_additional_data');
            Mage::getModel('amorderattach/order_field')->addField($data['type'], $data['fieldname']);
            Mage::helper('amorderattach')->clearCache();
        }
    }
    
    protected function _isJoined($from)
    {
        $found = false;
        foreach ($from as $alias => $data) {
            if ('attachments' === $alias) {
                $found = true;
            }
        }
        return $found;
    }
    
    protected function _getAttachments()
    {
        if (is_null($this->_attachments)) {
            $attachments = Mage::getModel('amorderattach/field')->getCollection();
            $attachments->addFieldToFilter('show_on_grid', 1);
            $this->_attachments = $attachments;
        }
        return $this->_attachments;
    }
    
    protected function _prepareCollection($collection, $place = 'order')
    {
        if ($this->_isJoined($collection->getSelect()->getPart('from')))
            return $collection;
            
        if (!$this->_isControllerName($place))
            return $collection;
        
        $attachments = $this->_getAttachments();
        
        if ($attachments->getSize()) {
            $isVersion14 = ! Mage::helper('ambase')->isVersionLessThan(1,4);
            $alias = $isVersion14 ? 'main_table' : 'e';
            $fields = array();
            foreach ($attachments as $attachment) {
                $fields[] = $attachment->getFieldname();
            }

            $collection->getSelect()
                       ->joinLeft(
                            array('attachments' => Mage::getModel('amorderattach/order_field')->getResource()->getTable('amorderattach/order_field')),
                            $alias . '.entity_id = attachments.order_id',
                            $fields
                       );
            
        }
        
        return $collection;
    }
    
    public function onCoreCollectionAbstractLoadBefore($observer)
    {
        $collection = $observer->getCollection();
        if ($this->_isInstanceOf($collection, $this->_orderCollectionClasses)) {
            $this->_prepareCollection($collection);
        }
    }
    
    protected function _prepareBackendHtml($html)
    {
        if (false === strpos($html, 'BEGIN `Amasty: Order Memos and Attachments`')) {
            $attachments = Mage::app()->getLayout()->createBlock('amorderattach/adminhtml_sales_order_view_attachment');
            $html = preg_replace('@<div class="box-left">(\s*)<!--Billing Address-->(\s*)<div class="entry-edit">(\s*)<div class="entry-edit-head">(\s*)(.*?)head-billing-address@', 
                                 $attachments->toHtml() .'<div class="box-left"><div class="entry-edit"><div class="entry-edit-head">$5head-billing-address', $html, 1);
        }
        return $html;
    }
    
    public function handleBlockOutput($observer)
    {
        $block = $observer->getBlock();
        
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        
        if ($this->_isInstanceOf($block, $this->_orderViewClasses)) {
            $html = $this->_prepareBackendHtml($html);
        }
        
        $transport->setHtml($html);
    }
    
    protected function _isControllerName($place)
    {
        $found = false;
        foreach ($this->_controllerNames as $controllerName) {
            if (false !== strpos(Mage::app()->getRequest()->getControllerName(), $controllerName . $place)) {
                $found = true;
            }
        }
        return $found;
    }
    
    protected function _prepareColumns(&$grid, $export = false, $place = 'order', $after = 'grand_total')
    {
        if (!$this->_isControllerName($place) || 
            !in_array(Mage::app()->getRequest()->getActionName(), $this->_permissibleActions) )
            return $grid;
        
        $attachments = $this->_getAttachments();
        
        if ($attachments->getSize() > 0) {
            foreach ($attachments as $attachment) {
                $type = $attachment->getType();
                if ($export && ('file' == $type || 'file_multiple' == $type)) {
                    continue;
                }
                $column = array(
                    'header'       => Mage::helper('amorderattach')->__($attachment->getLabel()),
                    'index'        => $attachment->getFieldname(),
                    'filter_index' => 'attachments.'.$attachment->getFieldname(),
                );
                switch ($type) {
                    case 'date':
                        $column['type']      = 'date';
                        $column['align']     = 'center';
                        $column['gmtoffset'] = false;
                        break;
                    case 'text':
                    case 'string':
                        $column['filter']   = 'adminhtml/widget_grid_column_filter_text';
                        $column['renderer'] = 'amorderattach/adminhtml_sales_order_grid_renderer_text' . ($export ? '_export' : '');
                        $column['sortable'] = true;
                        break;
                    case 'select':
                        $selectOptions = array();
                        $options       = explode(',', $attachment->getOptions());
                        $options       = array_map('trim', $options);
                        if ($options) {
                            foreach ($options as $option) {
                                $selectOptions[$option] = $option;
                            }
                        }
                        $column['type']    = 'options';
                        $column['options'] = $selectOptions;
                        break;
                    case 'file':
                        $column['filter']   = 'adminhtml/widget_grid_column_filter_text';
                        $column['renderer'] = 'amorderattach/adminhtml_sales_order_grid_renderer_file';
                        $column['sortable'] = true;
                        break;
                    case 'file_multiple':
                        $column['filter']   = 'adminhtml/widget_grid_column_filter_text';
                        $column['renderer'] = 'amorderattach/adminhtml_sales_order_grid_renderer_file_multiple';
                        $column['sortable'] = false;
                        break;
                }
                $grid->addColumnAfter($column['index'], $column, $after);
                $after = $column['index'];
            }
        }
        
        return $grid;
    }
    
    protected function _isInstanceOf($block, $classes)
    {
        $found = false;
        foreach ($classes as $className) {
            if ($block instanceof $className) {
                $found = true;
                break;
            }
        }
        return $found;
    }
    
    public function onCoreLayoutBlockCreateAfter($observer)
    {
        $block = $observer->getBlock();
        // Order Grid
        if ($this->_isInstanceOf($block, $this->_orderGridClasses)) {
            $this->_prepareColumns($block, in_array(Mage::app()->getRequest()->getActionName(), $this->_exportActions));
        }
    }
}