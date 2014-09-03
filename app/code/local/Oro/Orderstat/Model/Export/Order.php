<?php

class Oro_Orderstat_Model_Export_Order
{

    const FILENAME = 'ICEUSORDER_';
    const ORDER_BATCH_SIZE = 500;

    const ORDER_STATUS_PROCESSING_SHIPMENT = 'processing_shipment';
    const ORDER_STATUS_PENDING_SHIPMENT = 'pending_shipment';
    const ORDER_ADAPTER_PATH = 'orderstat/export_order_';
    protected $_exportFields = null;
    protected $_orderFileName = null;

    protected $_attribute = null;


    /**
     * extracts data from order and saves it in csv, then sends to ftp server.
     */
    public function export()
    {
        try{
            $orders = $this->getOrdersForExport();
            $adapters = array();
            $files = array();
            $exported = array();
            $vendorsUsed = array();
            if ($orders->count()) {

                $page = 0;
                while ($orders && $orders->count()) {
                    foreach ($orders as $order) {
                        /** @var $order Mage_Sales_Model_Order */
                        $orderItems = $order->getAllItems();

                        foreach ($orderItems as $item) {
                            $vendor = Mage::getModel('orderstat/vendor')->loadByName($item->getProduct()->getIceusVendor());
                            if(!$vendor->getId()){
                                $vendor = Mage::getModel('orderstat/vendor')->loadByName(Oro_Orderstat_Helper_Delmar::VENDOR);
                            }
                            $vendorCode = $vendor->getVendorCode();

                            if($order->getCustomMerchant() == Oro_Orderstat_Helper_Diamondcandles::CUSTOM_MERCHANT){
                                $vendorCode = Oro_Orderstat_Helper_Diamondcandles::VENDOR_CODE;
                            }

                            if(!isset($adapters[$vendorCode])){
                               $adapters[$vendorCode] = Mage::getModel(self::ORDER_ADAPTER_PATH.$vendorCode);
                            }

                            if(!isset($files[$vendorCode])){
                                $files[$vendorCode] = $this->getOrderFile($adapters[$vendorCode]);
                                fputcsv($files[$vendorCode], array_keys($adapters[$vendorCode]->getExportFields()), ',');
                                $vendorsUsed[$vendorCode] = $vendor->getVendorName();
                            }



                            /** @var $item Mage_Sales_Model_Order_item*/

                            //we have to send only simple products
                            if ($item->getHasChildren()) {
                                continue;
                            }

                            $exportItem = array();
                            foreach ($adapters[$vendorCode]->getExportFields() as $exportField) {
                                $value = false;
                                switch ($exportField['type']) {
                                    case 'order' :
                                        $object = $order;
                                        break;
                                    case 'item' :
                                        $object = $item;
                                        break;
                                    case 'parent_item' :
                                        $object = $item->getParentItem() ? $item->getParentItem() : $item;
                                        break;
                                    case 'address' :
                                        $object = $order->getShippingAddress();
                                        break;
                                    case 'const' :
                                        $value = $exportField['action'];
                                        break;
                                    default :
                                        $object = $this;
                                }

                                if ($value === false) {
                                    $action = $exportField['action'];
                                    if (method_exists($this, $action)) {
                                        $value = $this->$action($object);
                                    } else {
                                        $action = 'get' . uc_words($action, '');
                                        $value = $object->$action(
                                            isset($exportField['param'])?$exportField['param']:null
                                        );
                                    }
                                }

                                $exportItem[$exportField['name']] = trim($value);

                            }
                            fputcsv($files[$vendorCode], $exportItem, ',', '"');
                        }

                        $order->setSyncStatus(Oro_Orderstat_Helper_Data::ORDER_SYNC_STATUS_SENT);
                        $order->setState(
                            $order::STATE_PROCESSING,
                            static::ORDER_STATUS_PROCESSING_SHIPMENT,
                            Mage::helper('orderstat')->__(
                                'Shipping from %s has been requested. Exported to file %s',
                                implode(',', array_values($vendorsUsed)),
                                $this->getOrderFilename()
                            )
                        );
                        $order->save();

                        $exported[] = $order->getIncrementId();

                    }

                    $page++;
                    $orders = $this->getOrdersForExport($page);
                }
                foreach($files as $key => $file){
                    fclose($file);
                }

                foreach($adapters as $key => $adapter){
                    if($adapter->isActive()){
                        $result = $adapter->export($files[$key]);
                        if (!$result) {
                            Mage::helper('orderstat')->log('ERROR sending file ' . $this->getOrderFilename() . ' to '.$key, true);
                        } else {
                            Mage::helper('orderstat')->log('Orders sent to '. $vendorsUsed[$key]. ': ' . implode(', ' , $exported));
                        }
                    }else{
                        Mage::helper('orderstat')->log($vendorsUsed[$key] . ' is not active.  No order information sent to '. $vendorsUsed[$key]);
                    }
                }

            }
        }catch(Exception $e){
            Mage::helper('orderstat')->log($e->getMessage());
        }

    }

    /**
     * Gets a batch of orders for export
     *
     * @param int $page
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getOrdersForExport($page = 0)
    {
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToFilter('sync_status', Oro_Orderstat_Helper_Data::ORDER_SYNC_STATUS_APPROVED);

        $orders->getSelect()->limit(self::ORDER_BATCH_SIZE, self::ORDER_BATCH_SIZE * $page);

        return $orders;
    }
    /**
     * Get for file
     *
     * @param Oro_Orderstat_Model_Export_Order_[Distributor]
     * @return file resource stream
     */

    public function getOrderFile($adapter)
    {
        $adapter->getIo()->setTmpPath('Export');
        return $adapter->getIo()->tmpFile($adapter->getTmpFileName());
    }

    /**
     * Gets order filename
     *
     * @return string
     */
    public function getOrderFilename()
    {
        if (!$this->_orderFileName) {
            $this->_orderFileName = static::FILENAME
                . Mage::app()->getLocale()->Date()->toString(Mage::helper('orderstat')->getFilenameFormat())
                . '.csv';
        }

        return $this->_orderFileName;
    }

    /**
     * Gets product attribute by code
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($code)
    {
        if (!$this->_attribute) {
            $this->_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
        }

        return $this->_attribute;
    }



    /**
     * Gets shipping type
     *
     * @TODO: replace with carrier code + rate code
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getServiceType($order)
    {
        $shipping = explode(' - ', $order->getShippingDescription());

        return end($shipping);
    }

    /**
     * Get's carrier account
     * @param Mage_Sales_Model_Order $order
     * @return string
     */

    protected function _getShippingAccountNumber($order)
    {
        $shipDesc = $order->getShippingDescription();
        $helper = Mage::helper('orderstat');
        $acctNumber = $helper->getOrderConfig('ups');
        if(stripos($shipDesc, 'usps') !== FALSE){
            $acctNumber = $helper->getOrderConfig('usps');
        }else if (stripos($shipDesc, 'fedex') !== FALSE){
            $acctNumber = $helper->getOrderConfig('fedex');
        }
        return $acctNumber;
    }

    /**
     * Formats Order Date
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getOrderDate($order)
    {
        $date = new Zend_Date($order->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT);

        return $date->toString('dd/MM/YYYY');
    }

    /**
     * Gets ring size
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    protected function _getRingSize($item)
    {
        $size = '';
        $options = $item->getProductOptions();

        if (is_array($options) && isset($options['info_buyRequest'])
            && is_array($options['info_buyRequest']) && isset($options['info_buyRequest']['super_attribute'])
        ) {
            $attribute = $this->getAttribute('iceus_ring_size');
            foreach ($options['info_buyRequest']['super_attribute'] as $attributeId => $attributeOption) {
                if ($attributeId == $attribute->getId()) {
                    $size = $attribute->getSource()->getOptionText($attributeOption);
                }
            }
        }

        return $size;
    }

    /**
     * Formats item qty
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return float
     */
    protected function _getQty($item)
    {
        return round($item->getQtyOrdered());
    }
}