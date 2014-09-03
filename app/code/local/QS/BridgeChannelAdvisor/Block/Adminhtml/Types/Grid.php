<?php
/**
 * Adminhtml ChannelAdvisor Content Item Types Mapping grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('types_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('bridgechanneladvisor/type_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid colunms
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('attribute_set_name',
            array(
                'header'    => $this->__('Attributes Set'),
                'index'     => 'attribute_set_name',
        ));

        $this->addColumn('channel_category_name',
            array(
                'header'    => $this->__('ChannelAdvisor Category Name'),
                'index'     => 'channel_category_name',
        ));

        $this->addColumn('items_total',
            array(
                'header'    => Mage::helper('catalog')->__('Total Qty Content Items'),
                'width'     => '150px',
                'index'     => 'items_total',
                'filter'    => false
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId(), '_current'=>true));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
