<?php
abstract class Oro_Orderstat_Model_Import_Shipping_Abstract
{

    protected $_fileName = '_';
    protected $_configPath;
    protected $_lastSyncDate = null;

    /**
     * Get Helper
     */

    abstract public function getHelper();
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
            $prodVendorName = $item->getProduct()->getIceusVendor();

            if (isset($orderData['items'][$item->getVendorSku()]) && $prodVendorName == $this->getHelper()->getVendorName()) {
                $shipQtys[$item->getId()] = $orderData['items'][$item->getVendorSku()];
            }
        }

        /** @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($shipQtys);

        $shipmentComment = $this->getHelper()->__('Shipping has been confirmed by distributor.');
        // add tracking numbers
        foreach ($orderData['tracks'] as $trackingNumber => $data) {
            if ($trackingNumber) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->addData($data);

                $shipment->addTrack($track);
                $shipmentComment .= $this->getHelper()->__(
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
                $this->getHelper()->log("Payment had been captured for order #{$order->getIncrementId()}");
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
     * Loads filenames processed during last session
     *
     * @return array
     */
    public function getLastProcessed()
    {
        if ($this->_lastProcessed === null) {
            $this->_lastProcessed = array();

            $lastProcessed = Mage::getStoreConfig(
                Oro_Orderstat_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastProcessed'
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
                Oro_Orderstat_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastProcessed',
                implode(',', $lastProcessed)
            );
        }
    }

    public function getLastSyncDate()
    {
        if (!$this->_lastSyncDate) {
            $date =  $this->getHelper()->getModuleConfig($this->_configPath . '/lastSync');

            if (!$date) {
                $date = Mage::getSingleton('core/date')->gmtTimestamp();
                $date -= 60*60*24; // sub 1 day
            }

            $this->_lastSyncDate = $date;

        }

        return $this->_lastSyncDate;
    }

    /**
     * Sets Last Sync Date
     *
     * @param $date
     */
    public function setLastSyncDate($date)
    {
        $this->_lastSyncDate = $date;
    }

    /**
     * Save Last Sync Date
     *
     * @param int $lastSync
     */
    public function saveLastSyncDate($lastSync)
    {
        Mage::getConfig()->saveConfig(
            Oro_Orderstat_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastSync',
            $lastSync
        );

        Mage::app()->getCache()->clean();
    }

}