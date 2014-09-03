<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Orderstat module observer
 */
class Oro_Orderstat_Model_Observer
{

    /**
     * Exports orders by cron
     */
    public function orderExport()
    {
        if (Mage::helper('orderstat')->getGeneralConfig('is_active', true)
            && Mage::helper('orderstat')->getOrderConfig('auto', true)
        ) {
            $exporter = Mage::getModel('orderstat/export_order');
            $exporter->export();
        }
    }

    /**
     * Adds vendor_sku data to quote items. On sales_quote_product_add_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function addVendorSku(Varien_Event_Observer $observer)
    {
        foreach ($observer->getItems() as $item) {
            $product = $item->getProduct();

            $simpleOption = $product->getCustomOption('simple_product');
            if($simpleOption) {
                $optionProduct = $simpleOption->getProduct();
                if ($optionProduct) {
                    $product = $optionProduct;
                }
            }
            $item->setVendorSku(Mage::getResourceSingleton('catalog/product')
                    ->getAttributeRawValue(
                        $product->getId(),
                        Oro_Orderstat_Helper_Data::ATTRIBUTE_VENDOR_SKU,
                        Mage::app()->getStore()
                    )
            );
        }
    }

    /**
     * Add button to mark order for export to Orderstat
     *
     * @param Varien_Event_Observer $observer
     */
    public function addApproveShipmentButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View
            && Mage::getSingleton('admin/session')->isAllowed('admin/orderstat/shipment/approve')
        ) {
            if (!$block->getOrder()->getSyncStatus()) {
                $block->addButton('approve_shipment', array(
                    'label'     => Mage::helper('sales')->__('Add to shipping queue'),
                    'onclick'   => "confirmSetLocation('"
                        . Mage::helper('sales')->__('Are you sure you want to mark this order for export to all distributors?')
                        . "', '"
                        . $block->getUrl('orderstat/shipment/approve')
                        . "')",
                ));
            }
        }
    }
}
