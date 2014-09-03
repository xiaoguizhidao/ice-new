<?php

class Oro_Orderstat_Model_Observer_Accuratime
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
            $shipment = Mage::getModel('orderstat/import_shipping_accuratime');
            $shipment->update();
        }else{
            $this->getHelper()->log('Accuratime Shipment Import not enabled');
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
            $inventory = Mage::getModel('orderstat/import_inventory_accuratime');
            $inventory->update();
        }else{
            $this->getHelper()->log('Accuratime Inventory Import not enabled');
        }
    }

    public function getHelper()
    {
        if(!$this->_helper){
            $this->_helper = Mage::helper('orderstat/accuratime');
        }
        return $this->_helper;
    }
}