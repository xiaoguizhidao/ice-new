<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/
class Amasty_Audit_Block_Adminhtml_Auditlog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date_time');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amaudit/data')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection(); 

    }

    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('amaudit'); 
      
        $this->addColumn('date_time', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date_time',
            'type'      => 'datetime', 
            'width'     => '170px',
            'gmtoffset' => true,
            'default'    => ' ---- ',
            'filter'  => false,
        ));
       
        $this->addColumn('username', array(
            'header'    => $hlp->__('Username'),
            'index'     => 'username',
        ));
        
        $this->addColumn('name', array(
            'header'      => $hlp->__('Full Name'),
            'index'       => 'name',
        ));
        
        $this->addColumn('ip', array(
            'header'      => $hlp->__('IP Address'),
            'index'       => 'ip',
        )); 
        
        $this->addColumn('status', array(
            'header'      => $hlp->__('Status'),
            'index'       => 'status',
            'width'     => '120',
            'align'     => 'left',
            'type'      => 'options',
            'options'   => array(0 => $this->__('Failed'), 1 => $this->__('Success'), 2 => $this->__('Locked out')),
            'frame_callback' => array($this, 'decorateStatus')
        ));
        
        return parent::_prepareColumns();
    }
    
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case 0 :
            case 2 :
                $class = 'grid-severity-critical';
                break;
            case 1 :
                $class = 'grid-severity-notice';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }
}