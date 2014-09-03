<?php

class Oro_Delmar_Model_Export_Order_Diamondcandles extends Oro_Delmar_Model_Export_Order
{
/*
    const FILENAME = 'ICEUSORDER';
    const ORDER_BATCH_SIZE = 500;

    const ORDER_STATUS_PROCESSING_SHIPMENT = 'processing_shipment';
    const ORDER_STATUS_PENDING_SHIPMENT = 'pending_shipment';
*/
    /**
     * Gets a batch of diamond candle orders for export
     *
     * @param int $page
     * @return Mage__Sales_Model_Resource_Order_Collection
     */
    public function getOrdersForExport($page = 0)
    {
        $orders = Mage::getResourceModel('sales/order_collection')
        ->addAttributeToFilter('sync_status', Oro_Delmar_Helper_Diamondcandles::ORDER_SYNC_STATUS_APPROVED)
        ->addAttributeToFilter('custom_merchant', Oro_Delmar_Helper_Diamondcandles::CUSTOM_MERCHANT);

        $orders->getSelect()->limit(self::ORDER_BATCH_SIZE, self::ORDER_BATCH_SIZE * $page);
        return $orders;
    }



    /**
     * Gets Helper
     */
    public function getHelper()
    {
        return Mage::helper('delmar/diamondcandles');
    }

}