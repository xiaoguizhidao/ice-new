<?php

/**
 * Custom renderer for ChannelAdvisor license
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2014 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_System_Config_CaLicense extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('bridgechanneladvisor/adminhtml_profile/licenseRequest');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Check license')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();
        return $html;
    }

}