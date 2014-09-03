<?php
/**
 * ChannelAdvisor System Config
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @name       QS_BridgeChannelAdvisor_Model_System_Config
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_System_Config
{
    const IS_DIRECTION_CHANNEL_ADVISOR = 0;

    const IS_DIRECTION_MAGENTO = 1;

    const SAVE_PROTOCOL = 1;

    const UNSAVE_PROTOCOL = 0;


    public function isDirectionMagento()
    {
        return $this->getCaDataStoreConfig('direction') == self::IS_DIRECTION_MAGENTO;
    }

    public function isDirectionChannelAdvisor()
    {
        return $this->getCaDataStoreConfig('direction') == self::IS_DIRECTION_CHANNEL_ADVISOR;
    }

    public function getCaDataStoreConfig($path)
    {
        return Mage::getStoreConfig('bridgechanneladvisor/cadata/'. $path, Mage::app()->getStore());
    }

    public function saveProtocol()
    {
        return $this->getCaDataMainConfig('protocol') == self::SAVE_PROTOCOL;
    }

    public function unsaveProtocol()
    {
        return $this->getCaDataMainConfig('protocol') == self::UNSAVE_PROTOCOL;
    }

    public function getCaDataMainConfig($path)
    {
        return Mage::getStoreConfig('bridgechanneladvisor/bridgechanneladvisor/'. $path, Mage::app()->getStore());
    }


}