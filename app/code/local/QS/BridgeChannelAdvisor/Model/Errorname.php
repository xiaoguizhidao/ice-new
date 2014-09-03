<?php
/**
 * ChannelAdvisor Error Name Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Errorname extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/errorname");
    }

    public function getErrornameCollection()
    {
        return Mage::getModel('bridgechanneladvisor/errorname')->getCollection();
    }

}
