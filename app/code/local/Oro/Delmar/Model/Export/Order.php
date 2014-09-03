<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class for export orders to remote FTP
 */
class Oro_Delmar_Model_Export_Order
{
    const FILENAME = 'ICEUSORDER';
    const ORDER_BATCH_SIZE = 500;

    const ORDER_STATUS_PROCESSING_SHIPMENT = 'processing_shipment';
    const ORDER_STATUS_PENDING_SHIPMENT = 'pending_shipment';


    protected $_exportFields = null;
    protected $_orderFileName = null;

    protected $_attribute = null;


    /**
     * extracts data from order and saves it in csv, then sends to ftp server.
     */
    public function export()
    {
        $orders = $this->getOrdersForExport();

        $exported = array();

        if ($orders->count()) {
            /** @var Oro_Delmar_Model_Io $ftp */
            $ftp = Mage::getModel('delmar/io');
            $ftp->setTmpPath('Export');

            $file = $ftp->tmpFile('order_export');

            // header
            fputcsv($file, array_keys($this->_getExportFields()), ',');

            $page = 0;
            while ($orders && $orders->count()) {
                foreach ($orders as $order) {
                    /** @var $order Mage_Sales_Model_Order */

                    foreach ($order->getAllItems() as $item) {
                        /** @var $item Mage_Sales_Model_Order_item*/

                        //we have to send only simple products
                        if ($item->getHasChildren()) {
                            continue;
                        }

                        $exportItem = array();
                        foreach ($this->_getExportFields() as $exportField) {
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

                        fputcsv($file, $exportItem, ',', '"');
                    }

                    $order->setSyncStatus(Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_SENT);
                    $order->setState(
                        $order::STATE_PROCESSING,
                        self::ORDER_STATUS_PROCESSING_SHIPMENT,
                        $this->getHelper()->__(
                            'Shipping from Delmar has been requested. Exported to file %s',
                            $this->getOrderFilename()
                        )
                    );
                    $order->save();
                    $exported[] = $order->getIncrementId();
                }

                $page++;
                $orders = $this->getOrdersForExport($page);
            }

            fclose($file);

            $this->getHelper()->log('Exported ' . count($exported) . ' orders');

            $result = $ftp->connect($this->getHelper()->getOrderConfig('ftp_path'))
                ->send($this->getOrderFilename());
            $ftp->close();
            if (!$result) {
                $this->getHelper()->log('ERROR sending file ' . $this->getOrderFilename() . 'to Delmar FTP', true);
            } else {
                $this->getHelper()->log('Orders sent to Delmar: ' . implode(', ' , $exported));
            }


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
            ->addAttributeToFilter('sync_status', Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_APPROVED)
            ->addAttributeToFilter('custom_merchant', array('null' => true));

        $orders->getSelect()->limit(self::ORDER_BATCH_SIZE, self::ORDER_BATCH_SIZE * $page);

        return $orders;
    }

    /**
     * Gets order filename
     *
     * @return string
     */
    public function getOrderFilename()
    {
        if (!$this->_orderFileName) {
            $this->_orderFileName = self::FILENAME
                . Mage::app()->getLocale()->Date()->toString($this->getHelper()->getFilenameFormat())
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
     * Gets Helper
     */
    public function getHelper()
    {
        return Mage::helper('delmar');
    }
    /**
     * Gets order fields for export
     *
     * @return array
     */
    protected function _getExportFields()
    {
        if ($this->_exportFields === null) {
            $this->_exportFields = array();
            $orderConfigXML = new Varien_Simplexml_Element(
                file_get_contents(Mage::getModuleDir('etc', 'Oro_Delmar') . DS . 'order_export.xml')
            );

            foreach ($orderConfigXML->children() as $child) {
                $this->_exportFields[(string)$child->name] = $child->asArray();
            }
        }

        return $this->_exportFields;
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
