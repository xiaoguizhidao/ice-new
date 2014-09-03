<?php
/**
 * ChannelAdvisor System Config Flagset
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config_Source_Flagset
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config_Source_Flagset
{
    public function toOptionArray()
    {
        $flagsArr = array();

        $flagsArr[] = 'Yes';
        $flagsArr[] = 'UseFlag';

        return $flagsArr;
    }
}