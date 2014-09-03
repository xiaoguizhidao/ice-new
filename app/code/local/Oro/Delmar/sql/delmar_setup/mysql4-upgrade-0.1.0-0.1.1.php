<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$statuses = array(
    'processing_shipment' => 'Processing Shipment',
    'pending_shipment' => 'In Delmar Shipping Queue',
    'processing_payment' => 'Processing Payment'
);

foreach ($statuses as $orderStatus => $orderStatusLabel) {
    $status = Mage::getModel('sales/order_status');

    $status->setStatus($orderStatus)
        ->setLabel($orderStatusLabel)
        ->save();
    $status->assignState(Mage_Sales_Model_Order::STATE_PROCESSING);
}

$installer->endSetup();
