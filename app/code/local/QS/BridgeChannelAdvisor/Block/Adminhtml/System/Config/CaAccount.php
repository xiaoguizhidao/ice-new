<?php

/**
 * Custom renderer for ChannelAdvisor authorization
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_System_Config_CaAccount extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        //$url1 = Mage::getBaseUrl();
        //$this->getUrl('bridgechanneladvisor/adminhtml_profile/devrequest');
        //$url = $url1.'bridgechanneladvisor/adminhtml_profile/devrequest';
        $url = $this->getUrl('bridgechanneladvisor/adminhtml_profile/devrequest');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Get Developer Access')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();
        return $html;
    }

}