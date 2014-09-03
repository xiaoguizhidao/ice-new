<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Helper class for Orderstat module
 */
class Oro_Orderstat_Helper_Data extends Mage_Core_Helper_Abstract
{

    const CONFIG_PATH = 'orderstat';
    const CONFIG_PATH_GENERAL = 'general';
    const CONFIG_PATH_FTP = 'delmar_ftp';
    const CONFIG_PATH_ORDER = 'order';
    const CONFIG_PATH_SHIPMENT = 'shipment';
    const CONFIG_PATH_INVENTORY = 'inventory';

    const FILENAME_DATE_FORMAT = 'YYYYMMddhhmm';

    const ORDER_SYNC_STATUS_SENT = 1;
    const ORDER_SYNC_STATUS_SHIPPED = 2;
    const ORDER_SYNC_STATUS_APPROVED = 3;

    const ATTRIBUTE_VENDOR_SKU = 'iceus_fulfilment_sku';


    public function getModuleConfig($path, $isFlag = false)
    {
        if (!$isFlag) {
            return Mage::getStoreConfig(static::CONFIG_PATH . '/' . $path);
        } else {
            return Mage::getStoreConfigFlag(static::CONFIG_PATH . '/' . $path);
        }
    }

    public function getFtpConfig($field, $isFlag = false)
    {
        return $this->getModuleConfig(static::CONFIG_PATH_FTP . '/' . $field, $isFlag);
    }

    public function getGeneralConfig($field, $isFlag = false)
    {
        return $this->getModuleConfig(static::CONFIG_PATH_GENERAL . '/' . $field, $isFlag);
    }

    public function getOrderConfig($field, $isFlag = false)
    {
        return $this->getModuleConfig(static::CONFIG_PATH_ORDER . '/' . $field, $isFlag);
    }

    public function getShipmentConfig($field, $isFlag = false)
    {
        return $this->getModuleConfig(static::CONFIG_PATH_SHIPMENT . '/' . $field, $isFlag);
    }

    public function getInventoryConfig($field, $isFlag = false)
    {
        return $this->getModuleConfig(static::CONFIG_PATH_INVENTORY . '/' . $field, $isFlag);
    }

    public function getFilenameFormat()
    {
        return static::FILENAME_DATE_FORMAT;
    }

    /**
     * Log message on error or if debug mode enabled
     *
     * @param string $message
     * @param bool $error
     */
    public function log($message, $error = false)
    {
        //if ($error || $this->getGeneralConfig('delmar_debug', true)) {
            Mage::log($message, $error?Zend_Log::ERR:0, 'orderstat_integration.log');
        //}
    }

}
