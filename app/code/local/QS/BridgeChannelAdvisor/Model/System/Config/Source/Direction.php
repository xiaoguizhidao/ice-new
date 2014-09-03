<?php
/**
 * ChannelAdvisor System Config Direction
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config_Source_Direction
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config_Source_Direction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => QS_BridgeChannelAdvisor_Model_System_Config::IS_DIRECTION_MAGENTO,
                'label' => Mage::helper('adminhtml')->__('Magento -> ChannelAdvisor')
            ),
            array(
                'value' => QS_BridgeChannelAdvisor_Model_System_Config::IS_DIRECTION_CHANNEL_ADVISOR,
                'label' => Mage::helper('adminhtml')->__('ChannelAdvisor -> Magento')
            ),
        );
    }
}