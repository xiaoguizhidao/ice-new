<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/
class Amasty_Audit_Block_Adminhtml_Userlog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date_time');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amaudit/log')->getCollection();
        $collection->clearEmpty();
        $collection = Mage::getModel('amaudit/log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection(); 

    }

    protected function _prepareColumns()
    {
        $this->addExportType('*/*/exportCsv', Mage::helper('amaudit')->__('CSV'));                              
        $this->addExportType('*/*/exportXml', Mage::helper('amaudit')->__('XML'));
        
        $hlp = Mage::helper('amaudit'); 
      
        $this->addColumn('date_time', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date_time',
            'type'      => 'datetime', 
            'width'     => '180px',
            'gmtoffset' => true,
        ));
       
        $this->addColumn('username', array(
            'header'    => $hlp->__('Username'),
            'index'     => 'username',
            'align'     => 'center',
        ));
        
        $this->addColumn('fullname', array(
            'header'    => $hlp->__('Full name'),
            'index'     => 'username',
            'align'     => 'center',
            'frame_callback' => array($this, 'showFullName'),
        ));
        
        $this->addColumn('type', array(
            'header'      => $hlp->__('Action Type'),
            'index'       => 'type',
            'align'     => 'center',
            'filter'    => false,
            'frame_callback' => array($this, 'decorateStatus'),
        ));
        
        $this->addColumn('category_name', array(
            'header'      => $hlp->__('Object'),
            'index'       => 'category_name',
        ));
        
        
        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'skipEmptyStoresLabel' => 1,
                'sortable'      => true,
            ));
        }

        $this->addColumn('info', array(
            'header'      => $hlp->__('Item'),
            'index'       => 'info',
            'align'     => 'center',
            'filter'    => false,
            'renderer'  => 'amaudit/adminhtml_auditlog_renderer_name',
            'frame_callback' => array($this, 'showOpenElementUrl')
        )); 
        
         $this->addColumn('action',
            array(
                'header'    => $hlp->__('Actions'),
                'width'     => '170px',
                'align'     => 'center',
                'filter'    => false,
                'sortable'  => false,
                'frame_callback' => array($this, 'showActions')
        )); 
        
        
        return parent::_prepareColumns();
    }
    
    private function getStoreOptions(){
        $array = Mage::app()->getStores(true);
        $options = array();
        foreach($array as $key => $value){
              $options[$key] = $value->getName();  
        }
        return $options;
    }
    
    public function decorateStatus($value, $row, $column, $isExport)
    {
        return '<span class="amaudit-'.$value.'">'.$value.'</span>';
    }
    
  
    public function showOpenElementUrl($value, $row, $column, $isExport)
    {
        $category = $row->getCategory();
        $category =  str_replace("admin/", "adminhtml/", $category);
        $url = '';
        $mass = explode('/', $row->getParametrName());
        $params = array();
        if(array_key_exists(1, $mass)) {
            $params = array($mass[1] => $row->getElementId());
        }
        if($row->getElementId() && $category && $row->getType() != "Delete" && $row->getParametrName() && $row->getParametrName() != 'underfined') {
            if(strpos($row->getParametrName(), 'section') !== false) {
                $url = $this->getUrl($category . '/edit/' . $row->getParametrName());       
            }
            else {
                $url =  str_replace('index/', '', $this->getUrl($category . '/' . $mass[0], $params));         
            }
        }
        $view = "";
        if($row->getType() == "Edit") $view = '&nbsp<a href="'.$url.'"><span>[' . Mage::helper('amaudit')->__('view') . ']</span></a>';
        return '<span>'.$value.'</span>' . $view;
    }
    
    public function showActions($value, $row, $column, $isExport)
    {
        $preview = "";
        if($row->getType() == "Edit" || $row->getType() == "New") $preview = '<a class="amaudit-preview" id="'.$row->getId().'" onclick="buble.showToolTip(this); return false">' . Mage::helper('amaudit')->__('Preview Changes') . '</a><br>';
        return $preview . '<a href="'.$this->getUrl('*/*/edit', array('id' => $row->getId())).'"><span>' . Mage::helper('amaudit')->__('View Details') . '</span></a>';
    }
    
    public function showFullName($value, $row, $column, $isExport)
    {
        $username = $row->getUsername();
        if($username) {
            $user = Mage::getModel('admin/user')->loadByUsername($username);
            return $user->getName();
        }
        return '';
    }
    
    public function getRowUrl($row)
    {
          return $this->getUrl('*/*/edit', array('id' => $row->getId()));  
    }
    
}
