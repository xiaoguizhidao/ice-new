<?php
/**
 * ChannelAdvisor System Config Protocol Type
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config_Source_Protocol
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config_Source_Protocol
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => QS_BridgeChannelAdvisor_Model_System_Config::SAVE_PROTOCOL,
                'label' => Mage::helper('adminhtml')->__('https')
            ),
            array(
                'value' => QS_BridgeChannelAdvisor_Model_System_Config::UNSAVE_PROTOCOL,
                'label' => Mage::helper('adminhtml')->__('http')
            ),
        );
    }
}