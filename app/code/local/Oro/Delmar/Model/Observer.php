<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Delmar module observer
 */
class Oro_Delmar_Model_Observer
{

    /**
     * Exports orders by cron
     */
    public function orderExport()
    {
        if (Mage::helper('delmar/data')->getGeneralConfig('is_active', true)
            && Mage::helper('delmar/data')->getOrderConfig('auto', true)
        ) {
            $exporter = Mage::getModel('delmar/export_order');
            $exporter->export();
        }
    }

    /**
     * Export orders by cron
     */
    public function diamondCandlesOrderExport()
    {
        if(Mage::helper('delmar')->getGeneralConfig('is_active', true)
            && Mage::Helper('delmar/diamondcandles')->getOrderConfig('auto', true)
        ){

            $exporter = Mage::getModel('delmar/export_order_diamondcandles');
            $exporter->export();
         }
    }

    /**
     * Exports orders by cron
     */
    public function shipmentImport()
    {
        if (Mage::helper('delmar')->getGeneralConfig('is_active', true)
            && Mage::helper('delmar')->getShipmentConfig('auto', true)
        ) {
            $shipment = Mage::getModel('delmar/import_shipment');
            $shipment->update();
        }
    }

    /**
     * Exports orders by cron
     */
    public function inventoryImport()
    {
        if (Mage::helper('delmar')->getGeneralConfig('is_active', true)
            && Mage::helper('delmar')->getInventoryConfig('auto', true)
        ) {
            $inventory = Mage::getModel('delmar/import_inventory');
            $inventory->update();
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
                    Oro_Delmar_Helper_Data::ATTRIBUTE_VENDOR_SKU,
                    Mage::app()->getStore()
                )
            );
        }
    }

    /**
     * Add button to mark order for export to Delmar
     *
     * @param Varien_Event_Observer $observer
     */
    public function addApproveShipmentButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View
            && Mage::getSingleton('admin/session')->isAllowed('admin/delmar/shipment/approve')
        ) {
            if (!$block->getOrder()->getSyncStatus()) {
                $block->addButton('approve_shipment', array(
                    'label'     => Mage::helper('sales')->__('Add to Delmar shipping queue'),
                    'onclick'   => "confirmSetLocation('"
                        . Mage::helper('sales')->__('Are you sure you want to mark this order for export to Delmar?')
                        . "', '"
                        . $block->getUrl('delmar/shipment/approve')
                        . "')",
                ));
            }
        }
    }
}
