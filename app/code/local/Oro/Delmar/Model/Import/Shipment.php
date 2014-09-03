<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class for import shipments from remote FTP
 */
class Oro_Delmar_Model_Import_Shipment extends Oro_Delmar_Model_Import_Abstract
{

    const FIELD_ORDER_NUMBER = 0;
    const FIELD_VENDOR_SKU = 2;
    const FIELD_QTY = 3;
    const FIELD_STATUS = 4;
    const FIELD_SHIP_DATE = 6;
    const FIELD_CARRIER = 7;
    const FIELD_TRACK_NUMBER = 9;


    protected $_fileName = 'ICEUSSHIPPED';
    protected $_configPath = Oro_Delmar_Helper_Data::CONFIG_PATH_SHIPMENT;

    protected $_carriers = array();
    protected $_lastProcessed = null;


    /**
     * Create shipments for order. One order - one shipping for now
     *
     * @TODO: Add support for partial shipping
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
                Mage::helper('delmar')->log(
                    "ERROR creating shipment for order #{$orderIncrementId}. No order found in Magento",
                    true
                );

                continue;
            }

            try {
                $shipment = $this->_createShipment($order, $orderData);

                $shipment->getOrder()
                    ->setStatus('processing_payment')
                    ->setSyncStatus(Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_SHIPPED);

                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($order)
                    ->save();

                if (Mage::helper('delmar')->getModuleConfig($this->_configPath . '/capture', true)) {
                    $this->_createInvoice($order);
                    $order->save();
                }

                Mage::helper('delmar')->log("Shipment created for order #{$orderIncrementId}");
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::helper('delmar')->log(
                    "ERROR creating shipment for order #{$orderIncrementId}. " . $e->getMessage(),
                    true
                );
            }
        }
    }

    /**
     * Creates shipment for order
     *
     * @param Mage_Sales_Model_Order $order
     * @param array $orderData
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _createShipment($order, $orderData)
    {
        // format qty array
        $orderItems = $order->getItemsCollection(array(), true);
        $shipQtys = array();
        foreach ($orderItems as $item) {
            if (isset($orderData['items'][$item->getVendorSku()])) {
                $shipQtys[$item->getId()] = $orderData['items'][$item->getVendorSku()];
            }
        }

        /** @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($shipQtys);

        $shipmentComment = Mage::helper('delmar')->__('Shipping has been confirmed by Delmar.');
        // add tracking numbers
        foreach ($orderData['tracks'] as $trackingNumber => $data) {
            if ($trackingNumber) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->addData($data);

                $shipment->addTrack($track);
                $shipmentComment .= Mage::helper('delmar')->__(
                    '<br> Carrier: %s. Tracking Number: %s.',
                    $data['carrier_code'],
                    $trackingNumber
                );
            }
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);

        $order->addStatusHistoryComment($shipmentComment);

        return $shipment;
    }

    /**
     * Captures payment
     *
     * @param Mage_Sales_Model_order $order
     */
    public function _createInvoice($order)
    {
        $totallyShipped = true;
        foreach ($order->getAllVisibleItems() as $item) {
            if ($item->getQtyToShip()) {
                $totallyShipped = false;
            }
        }

        if ($totallyShipped) {
            if (!$order->getInvoiceCollection()->count()) {
                $order->getPayment()->capture(null);
                Mage::helper('delmar')->log("Payment had been captured for order #{$order->getIncrementId()}");
            }
        }
    }

    public function getCarrierCode($code)
    {
        $code = explode(',', $code);

        return strtolower(trim($code[0]));
    }

    /**
     * Fetches Carrier Title from config
     *
     * @param string $code
     * @return mixed
     */
    public function getCarrierTitle($code)
    {
        if (!isset($this->_carriers[$code])) {
            $this->_carriers[$code] = Mage::getStoreConfig("carriers_{$code}_title");
        }

        return $this->_carriers[$code];
    }

    /**
     * Import batch of files
     *
     * @param Oro_Delmar_Model_Io $ftp
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
                Mage::helper('delmar')->log('Processing file:' . $file['text']);

                $ftp->read($file['text'], $ftp->tmpFile($this->_configPath, false));
                $this->import($ftp->tmpFile($this->_configPath, 'r'));

                $imported[] = $file['text'];

                if ($date > $lastDate) {
                    $lastDate = $date;
                }
            } else {
                Mage::helper('delmar')->log('File:' . $file['text'] . ' older than last sync ('.date('Y-m-d H:i', $lastSync).'), skipping...');
            }
        }

        $this->saveLastProcessed($imported, $lastDate);
        $this->setLastSyncDate($lastDate);

        return count($imported);
    }

    /**
     * Loads filenames processed during last session
     *
     * @return array
     */
    public function getLastProcessed()
    {
        if ($this->_lastProcessed === null) {
            $this->_lastProcessed = array();

            $lastProcessed = Mage::getStoreConfig(
                Oro_Delmar_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastProcessed'
            );

            if (strlen($lastProcessed) > 0) {
                $this->_lastProcessed = explode(',', $lastProcessed);
            }

        }
        return $this->_lastProcessed;
    }

    /**
     * Saves processed filenames
     *
     * @param $lastProcessed
     */
    public function saveLastProcessed($lastProcessed, $lastDate)
    {
        if ($lastProcessed) {
            foreach ($this->getLastProcessed() as $fileName) {
                $parts = array();
                preg_match('/^'.$this->_fileName.'(.{12})(|.{6})\.csv/', $fileName, $parts);
                $date = new Zend_Date($parts[1], 'YYYYMMddHHmm');

                if ($date->getTimestamp() == $lastDate) {
                    $lastProcessed[] = $fileName;
                }
            }

            Mage::getConfig()->saveConfig(
                Oro_Delmar_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastProcessed',
                implode(',', $lastProcessed)
            );
        }
    }

}
