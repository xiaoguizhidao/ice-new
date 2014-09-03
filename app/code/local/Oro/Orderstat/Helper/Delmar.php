<?php
class Oro_Orderstat_Helper_Delmar extends Oro_Orderstat_Helper_Data
{
    const CONFIG_PATH = 'orderstat';
    const CONFIG_PATH_GENERAL = 'delmar_general';
    const CONFIG_PATH_FTP = 'delmar_ftp';
    const CONFIG_PATH_ORDER = 'delmar_order';
    const CONFIG_PATH_SHIPMENT = 'delmar_shipment';
    const CONFIG_PATH_INVENTORY = 'delmar_inventory';
    const VENDOR = 'Delmar Mfg. LLC';

    public function getVendorName()
    {
        $vendor = Mage::getModel('orderstat/vendor')->loadByCode('delmar');
        return $vendor->getVendorName();
    }
}