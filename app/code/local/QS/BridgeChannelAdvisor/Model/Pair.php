<?php
/**
 * ChannelAdvisor Pair Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Pair extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/pair");
    }

    public function getAttributesPairCollection()
    {
        return Mage::getModel('bridgechanneladvisor/pair')->getCollection();
    }

}