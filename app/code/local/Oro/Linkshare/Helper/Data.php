<?php
/**
 * @category   Oro
 * @package    Oro_Linkshare
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Linkshare_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    const XML_PATH_MERCHANT_ID = 'promo/linkshare/merchant_id';
    const XML_PATH_PREFIX = 'promo/linkshare/';

    /**
     * @return string
     */
    public function getMid()
    {
        return Mage::getStoreConfig(self::XML_PATH_MERCHANT_ID);
    }

    /**
     * @param $field
     * @return bool
     */
    public function getConfigFlag($field)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PREFIX . $field);
    }

    /**
     * @param $field
     * @return string
     */
    public function getConfig($field)
    {
        return Mage::getStoreConfig(self::XML_PATH_PREFIX . $field);
    }
}
