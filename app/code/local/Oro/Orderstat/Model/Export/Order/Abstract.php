<?php

abstract class Oro_Orderstat_Model_Export_Order_Abstract
{

    const FILENAME = 'ICEUSORDER';
    const ORDER_BATCH_SIZE = 500;

    const ORDER_STATUS_PROCESSING_SHIPMENT = 'processing_shipment';
    const ORDER_STATUS_PENDING_SHIPMENT = 'pending_shipment';

    protected $_exportFields = null;
    protected $_orderFileName = null;
    protected $_io = null;
    protected $_attribute = null;

    public function getTmpFileName()
    {
        return 'export_orders_'.static::PRODUCT_DISTRIBUTOR;
    }

    public function setIo($io)
    {
        $this->_io = $io;
    }
    public function getIo()
    {
        if(!$this->_io){
            $this->setIo(Mage::getModel('orderstat/io'));
        }
        return $this->_io;
    }

    public function getOrderFilename()
    {
        if (!$this->_orderFileName) {
            $this->_orderFileName = static::FILENAME
                . Mage::app()->getLocale()->Date()->toString(Mage::helper('orderstat')->getFilenameFormat())
                . '.csv';
        }

        return $this->_orderFileName;
    }


    /*
     * Export orders method
     *
     * @param string csv file name
     */
    abstract public function export($file);

    /*
     * Get helper
     *
     */

    abstract public function getHelper();

    abstract public function isActive();
}