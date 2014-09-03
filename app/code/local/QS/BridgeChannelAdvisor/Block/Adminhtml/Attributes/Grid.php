<?php
/**
 * ChannelAdvisor Items
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Attributes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('channel_attribute_id');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Attributes_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bridgechanneladvisor/attribute')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Attributes_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('attribute_id',
            array(
                'header'    => $this->__('ID'),
                'width'     => '3%',
                'index'     => 'attribute_id',
        ));

        $this->addColumn('attribute_name',
            array(
                'header'    => $this->__('Attribute Name'),
                'width'     => '40%',
                'index'     => 'attribute_name',
        ));

        $this->addColumn('attribute_data',
            array(
                'header'    => $this->__('Uploaded At'),
                'type'      => 'datetime',
                'width'     => '100px',
                'index'     => 'uploaded_at',
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
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
