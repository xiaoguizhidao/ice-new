<?php
/**
 * ChannelAdvisor Item
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Item extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('items');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Item
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bridgechanneladvisor/item')->getCollection();
        $store = $this->_getStore();
        $collection->addStoreFilter($store->getId());
        $collection->_joinMageProductName();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items_Item
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id',
            array(
                'header'    => $this->__('Product ID'),
                'width'     => '3%',
                'index'     => 'product_id',
        ));

        $this->addColumn('name',
            array(
                'header'    => $this->__('Product Name'),
                'width'     => '70%',
                'index'     => 'name',
        ));

        $this->addColumn('expires',
            array(
                'header'    => $this->__('Uploaded to ChannelAdvisor'),
                'type'      => 'datetime',
                'width'     => '100px',
                'index'     => 'published',
        ));

        return parent::_prepareColumns();
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
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        //return $this->getUrl('*/*/grid', array('_current'=>true));
        return $this->getUrl('*/adminhtml_items/itemGrid', array('index' => $this->getIndex(),'_current'=>true));
    }

    /**
     * Prepare grid massaction actions
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item');
        $this->setNoFilterMassactionColumn(true);

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => $this->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete', array('_current'=>true)),
             'confirm'  => $this->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('refresh', array(
             'label'    => $this->__('Synchronize'),
             'url'      => $this->getUrl('*/*/refresh', array('_current'=>true))
        ));
        return $this;
    }

}
