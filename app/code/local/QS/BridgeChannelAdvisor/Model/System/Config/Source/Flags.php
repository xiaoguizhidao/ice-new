<?php
/**
 * ChannelAdvisor System Config Flags
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config_Source_Flags
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config_Source_Flags
{
    public function toOptionArray()
    {
        $flagsArr = array();

        $flagsArr[] = 'NoFlag';
        $flagsArr[] = 'ExclamationPoint';
        $flagsArr[] = 'QuestionMark';
        $flagsArr[] = 'NotAvailable';
        $flagsArr[] = 'Price';
        $flagsArr[] = 'BlueFlag';
        $flagsArr[] = 'GreenFlag';
        $flagsArr[] = 'RedFlag';
        $flagsArr[] = 'YellowFlag';
        $flagsArr[] = 'ItemCopied';

        return $flagsArr;
    }
}