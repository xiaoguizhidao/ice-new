<?php
/**
 * ChannelAdvisor Relationships model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Relationships extends Mage_Core_Model_Config_Data
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/relationships");
    }

}
