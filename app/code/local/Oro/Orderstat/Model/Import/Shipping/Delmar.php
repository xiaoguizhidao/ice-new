<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class for import shipments from remote FTP
 */
class Oro_Orderstat_Model_Import_Shipping_Delmar extends Oro_Orderstat_Model_Import_Shipping_Abstract
{

    const FIELD_ORDER_NUMBER = 0;
    const FIELD_VENDOR_SKU = 2;
    const FIELD_QTY = 3;
    const FIELD_STATUS = 4;
    const FIELD_SHIP_DATE = 6;
    const FIELD_CARRIER = 7;
    const FIELD_TRACK_NUMBER = 9;


    protected $_fileName = 'ICEUSSHIPPED';
    protected $_configPath = Oro_Orderstat_Helper_Delmar::CONFIG_PATH_SHIPMENT;

    protected $_carriers = array();
    protected $_lastProcessed = null;

    protected $_helper = '';

    public function getHelper()
    {
        if(!$this->_helper){
            $this->_helper = Mage::helper('orderstat/delmar');
        }
        return $this->_helper;
    }

    public function update()
    {
        /** @var Oro_Orderstat_Model_Io $ftp */
        $ftp = Mage::getModel('orderstat/io')
            ->setHelper($this->getHelper())
            ->setTmpPath('Import')
            ->connect($this->getHelper()->getModuleConfig($this->_configPath . '/ftp_path'));

        $imported = $this->_batchImport($ftp, $this->getLastSyncDate());

        $ftp->close();

        if ($imported && $this->getHelper()->getModuleConfig($this->_configPath . '/reindex', true)) {
            $indexes = array('cataloginventory_stock', 'catalog_product_price');
            foreach ($indexes as $index) {
                $process = Mage::getModel('index/indexer')->getProcessByCode($index);
                $process->reindexAll();
            }

            Mage::getSingleton('varnishcache/control')->clean(Mage::helper('varnishcache/cache')->getStoreDomainList());
        }

        if ($imported) {
            $this->saveLastSyncDate($this->getLastSyncDate());
        }
    }

    /**
     * Create shipments for order. One order - one shipping for now
     *
     * @param resource $file
     */
    public function import($file)
    {
        $shipmentData = array();

        // extract header (not used for now)
        $header = fgetcsv($file);

        // extract data and group by order id
        while ($row = fgetcsv($file)) {
            if (!isset($shipmentData[$row[self::FIELD_ORDER_NUMBER]])) {
                $shipmentData[$row[self::FIELD_ORDER_NUMBER]] = array(
                    'data' => array(),
                    'track' => array()
                );
            }

            $shipmentData[$row[self::FIELD_ORDER_NUMBER]]['items'][trim($row[self::FIELD_VENDOR_SKU])]
                = trim($row[self::FIELD_QTY]);

            $carrierCode = $this->getCarrierCode($row[self::FIELD_CARRIER]);
            $shipmentData[trim($row[self::FIELD_ORDER_NUMBER])]['tracks'][trim($row[self::FIELD_TRACK_NUMBER])] = array(
                'carrier_code' => $carrierCode,
                'title' => $this->getCarrierTitle($carrierCode),
                'number' => trim($row[self::FIELD_TRACK_NUMBER])
            );
        }

        foreach ($shipmentData as $orderIncrementId => $orderData) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if (!$order->getId()) {
                $this->getHelper()->log(
                    "ERROR creating shipment for order #{$orderIncrementId}. No order found in Magento",
                    true
                );

                continue;
            }

            try {
                $shipment = $this->_createShipment($order, $orderData);

                $shipment->getOrder()
                    ->setStatus('processing_payment')
                    ->setSyncStatus(Oro_Orderstat_Helper_Data::ORDER_SYNC_STATUS_SHIPPED);

                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($order)
                    ->save();

                if ($this->getHelper()->getModuleConfig($this->_configPath . '/capture', true)) {
                    $this->_createInvoice($order);
                    $order->save();
                }

                $this->getHelper()->log("Shipment created for order #{$orderIncrementId}");
            } catch (Exception $e) {
                Mage::logException($e);
                $this->getHelper()->log(
                    "ERROR creating shipment for order #{$orderIncrementId}. " . $e->getMessage(),
                    true
                );
            }
        }
    }

     /**
     * Import batch of files
     *
     * @param Oro_Orderstat_Model_Io $ftp
     * @param int $lastSync
     * @return int
     */
    protected function _batchImport($ftp, $lastSync)
    {
        $lastDate = $lastSync;

        $imported = array();

        foreach ($ftp->ls('/^'.$this->_fileName.'(.{12})(|.{6})\.csv/') as $file) {
            $date = new Zend_Date($file['found'][1], 'YYYYMMddHHmm');
            $date = $date->getTimestamp();

            if ($date >= $lastSync && !in_array($file['text'], $this->getLastProcessed())) {
                $this->getHelper()->log('Processing file:' . $file['text']);

                $ftp->read($file['text'], $ftp->tmpFile($this->_configPath, false));
                $this->import($ftp->tmpFile($this->_configPath, 'r'));

                $imported[] = $file['text'];

                if ($date > $lastDate) {
                    $lastDate = $date;
                }
            } else {
                $this->getHelper()->log('File:' . $file['text'] . ' older than last sync ('.date('Y-m-d H:i', $lastSync).'), skipping...');
            }
        }

        $this->saveLastProcessed($imported, $lastDate);
        $this->setLastSyncDate($lastDate);

        return count($imported);
    }


}
