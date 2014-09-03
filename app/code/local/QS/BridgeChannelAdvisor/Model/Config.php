<?php
/**
 * ChannelAdvisor Config model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Config extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        Mage::getResourceModel('bridgechanneladvisor/attribute')->importAndUpload();
        Mage::getResourceModel('bridgechanneladvisor/relationships')->relationshipsUpload();
    }

}
