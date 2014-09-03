<?php

class Oro_Orderstat_Model_Export_Order_Diamondcandles extends Oro_Orderstat_Model_Export_Order_Delmar
{

        const FILENAME = 'ICEUSORDER';
        const ORDER_BATCH_SIZE = 500;

        const ORDER_STATUS_PROCESSING_SHIPMENT = 'processing_shipment';
        const ORDER_STATUS_PENDING_SHIPMENT = 'pending_shipment';

    /**
     * Gets Helper
     */
    public function getHelper()
    {
        return Mage::helper('orderstat/diamondcandles');
    }

}