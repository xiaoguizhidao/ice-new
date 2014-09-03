<?php
/**
 * ChannelAdvisor Attributes Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Ship extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/ship");
    }

    /**
     * Get ChannelAdvisor Shipment Collection
     * @return object
     */
    public function getCaShipCollection()
    {
        return Mage::getModel('bridgechanneladvisor/ship')->getCollection();
    }

}
