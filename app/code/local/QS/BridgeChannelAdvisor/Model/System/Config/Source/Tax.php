<?php
/**
 * ChannelAdvisor System Config Tax
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config_Source_Tax
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config_Source_Tax
{
    public function toOptionArray()
    {
        $arr = array();
        $taxArr = array();
        $taxClass = Mage::getModel('tax/class')->getCollection()->addFieldToFilter('class_type', 'PRODUCT');
        $taxArr[0] = 'None';
        foreach($taxClass as $tax){
            $taxArr[$tax->getId()]['value'] = $tax->getId();
            $taxArr[$tax->getId()]['label'] = $tax->getClassName();
        }
        $arr = $taxArr;
        return $arr;
    }
}