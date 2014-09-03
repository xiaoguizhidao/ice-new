<?php
class Oro_Orderstat_Model_Observer_Delmar
{

    protected $_helper;
    /**
     * Exports orders by cron
     */
    public function shipmentImport()
    {
        if ($this->getHelper()->getGeneralConfig('is_active', true)
            && $this->getHelper()->getShipmentConfig('auto', true)
        ) {
            $shipment = Mage::getModel('orderstat/import_shipping_delmar');
            $shipment->update();
        }else{
            $this->getHelper()->log('Delmar Shipment Import not enabled');
        }
    }

    /**
     * Exports orders by cron
     */
    public function inventoryImport()
    {
        if ($this->getHelper()->getGeneralConfig('is_active', true)
            && $this->getHelper()->getInventoryConfig('auto', true)
        ) {
            $inventory = Mage::getModel('orderstat/import_inventory_delmar');
            $inventory->update();
        }else{
            $this->getHelper()->log('Delmar Inventory Import not enabled');
        }
    }

    public function getHelper()
    {
        if(!$this->_helper){
            $this->_helper = Mage::helper('orderstat/delmar');
        }
        return $this->_helper;
    }
}