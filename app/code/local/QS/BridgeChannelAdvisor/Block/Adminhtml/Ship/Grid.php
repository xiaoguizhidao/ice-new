<?php
/**
 * Adminhtml ChannelAdvisor Shipping Mapping grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Ship_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ship_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Ship_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('bridgechanneladvisor/shiptype_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid colunms
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_ShipType_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('mage_carrier_code',
            array(
                'header'    => $this->__('Mage Carrier'),
                'index'     => 'mage_carrier_code',
        ));

        $this->addColumn('channel_carrier_name',
            array(
                'header'    => $this->__('ChannelAdvisor Carrier'),
                'index'        => array('channel_carrier_name', 'channel_carrier_class_name'),
                'type'         => 'concat',
                'separator'    => ' - ',
                'filter_index' => "CONCAT(ca_ship.carrier_name, ' - ', ca_ship.class_name)",
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
        return $this->getUrl('*/*/edit', array('ship_type_id'=>$row->getId(), '_current'=>true));
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
