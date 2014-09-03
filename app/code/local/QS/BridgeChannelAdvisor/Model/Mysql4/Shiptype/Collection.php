<?php
/**
 * ChannelAdvisor ShipTypes collection
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Shiptype_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init("bridgechanneladvisor/shiptype");
    }

    /**
     * Init collection select
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Shiptype_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinChannelAdvisorCarrier();
        return $this;
    }

    /**
     * Join ChannelAdvisor Shipment Data
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Shiptype_Collection
     */
    public function _joinChannelAdvisorCarrier()
    {
        $this->getSelect()
            ->join(
                array('ca_ship'=>$this->getTable('bridgechanneladvisor/ship')),
                'main_table.carrier_id=ca_ship.carrier_id',
                array('channel_carrier_name' => 'ca_ship.carrier_name',
                      'channel_carrier_class_name'=>'ca_ship.class_name',
                ));

        return $this;
    }

    public function filterByUsedMethods()
    {

    }

}
