<?php
/**
 * ChannelAdvisor Pair collection
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Pair_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init("bridgechanneladvisor/pair");
    }

}
