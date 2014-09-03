<?php
/**
 * Products Grid to add to ChannelAdvisor Content
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Product extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('channeladvisor_selection_search_grid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Before rendering html
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Product
     */
    protected function _beforeToHtml()
    {
        $this->setId($this->getId().'_'.$this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
        return parent::_beforeToHtml();
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Product
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->_getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id');

        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }

        $resource = Mage::getSingleton('core/resource');
        $table_prepare = $resource->getTableName('bridgechanneladvisor/prepareitem');
        $table_item = $resource->getTableName('bridgechanneladvisor/items');

        $collection->getSelect()->where('e.channeladvisor_product = 0 AND e.entity_id NOT IN (select cpi.product_id from '. $table_prepare .' as cpi) AND e.entity_id NOT IN (select cai.product_id from '. $table_item .' as cai)');
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Product
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'index'     => 'name',
            'column_css_class'=> 'name'
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'column_css_class'=> 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->_getStore()->getDefaultCurrencyCode(),
            'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getDefaultCurrencyCode()),
            'index'     => 'price'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_items/grid', array('index' => $this->getIndex(),'_current'=>true));
    }

    /**
     * Get array with product ids, which was exported to ChannelAdvisor Content
     *
     * @return array
     */
    protected function _getChannelAdvisorProductIds()
    {
        $collection = Mage::getResourceModel('bridgechanneladvisor/item_collection')
            ->addStoreFilter($this->_getStore()->getId())
            ->load();
        $productIds = array();
        foreach ($collection as $item) {
            $productIds[] = $item->getProductId();
        }
        return $productIds;
    }

    /**
     * Get array with product ids, which added to queue for exporting to ChannelAdvisor Content
     *
     * @return array
     */
    protected function _getChannelAdvisorPrepareProductIds()
    {
        $collection = Mage::getResourceModel('bridgechanneladvisor/prepareitem_collection')
            ->addStoreFilter($this->_getStore()->getId())
            ->load();
        $productIds = array();
        foreach ($collection as $item) {
            $productIds[] = $item->getProductId();
        }
        return $productIds;
    }

    /**
     * Get store model by request param
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore($this->getRequest()->getParam('store'));
    }

    /**
     * Prepare grid massaction actions
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('add', array(
             'label'    => $this->__('Add to Export Queue'),
             'url'      => $this->getUrl('*/*/massAdd', array('_current'=>true)),
        ));
        return $this;
    }

}
