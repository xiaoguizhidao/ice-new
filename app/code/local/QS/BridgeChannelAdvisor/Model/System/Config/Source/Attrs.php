<?php
/**
 * ChannelAdvisor System Config Attrs
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config_Source_Attrs
 * @copyright  Copyright (c) 2014 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config_Source_Attrs
{
    public function toOptionArray()
    {
        $collection = Mage::getModel('bridgechanneladvisor/attribute')->getCollection();
        $attrs = array();
        if(count($collection) > 0){
            foreach($collection as $simpleAttribute){
                $attrs[$simpleAttribute->getAttributeId()] = $simpleAttribute->getAttributeName();
            }
        }
        return $attrs;
    }
}