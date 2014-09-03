<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Delmar Shipment controller
 */
class Oro_Delmar_Adminhtml_ShipmentController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Mark order for export to Delmar
     */
    public function approveAction()
    {
        /** @var Oro_Delmar_Helper_Data $helper */
        $helper = Mage::helper('delmar');

        $orderId = $this->getRequest()->getParam('order_id');

        if ($orderId) {
            try {
                $this->_markForExport($orderId);
                $this->_getSession()->addSuccess(
                    $helper->__('Order was added to Delmar shipment queue')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError($helper->__('No Order ID specified'), $orderId);
        }

        $this->_redirectReferer();
    }

    /**
     * Mass action for mark order for export to Delmar
     */
    public function massApproveAction()
    {
        /** @var Oro_Delmar_Helper_Data $helper */
        $helper = Mage::helper('delmar');

        $orderIds = $this->getRequest()->getParam('order_ids');

        if ($orderIds) {
            try {
                foreach ($orderIds as $id) {
                    $this->_markForExport($id);
                }
                $this->_getSession()->addSuccess($helper->__('Orders were added to Delmar shipment queue'));

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirectReferer();
    }

    /**
     * @param int $orderId
     */
    protected function _markForExport($orderId)
    {
        /** @var Oro_Delmar_Helper_Data $helper */
        $helper = Mage::helper('delmar');

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            Mage::throwException($helper->__('Order %s not found', $orderId));
        }

        switch ($order->getSyncStatus()) {
            case Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_SENT :
            case Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_APPROVED :
                Mage::throwException($helper->__('Order %s already in shipment queue', $order->getIncrementId()));
                break;

            case Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_SHIPPED :
                Mage::throwException($helper->__('Order %s already shipped', $order->getIncrementId()));
                break;

            default :
                $order->setSyncStatus(Oro_Delmar_Helper_Data::ORDER_SYNC_STATUS_APPROVED);
                $order->setState('processing', Oro_Delmar_Model_Export_Order::ORDER_STATUS_PENDING_SHIPMENT);
                $order->save();
        }
    }

}
