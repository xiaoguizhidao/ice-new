<?php

abstract class Oro_Orderstat_Model_Import_Inventory_Abstract
{

    /**
     * Updates inventory
     *
     * @param resource $file
     */
    public function import($file)
    {
        // extract header (not used for now)
        $header = fgetcsv($file);

        $originalData = array();
        // load file into memory
        if (isset($file) && is_resource($file)) {
            while (feof($file) === false) {
                $row = fgetcsv($file);
                if (empty($row)) {
                    continue;
                }

                $vendorSku = strtoupper(trim($row[static::FIELD_VENDOR_SKU]));
                $originalData[$vendorSku] = (int)$row[static::FIELD_QTY];
            }

            $stockId   = (int)Mage::getSingleton('cataloginventory/stock')->getId();
            $stockData = array();
            $indexer = Mage::getModel('orderstat/indexer_vendorsku');
            // convert SKU to ID
            foreach (array_chunk($originalData, 1000, true) as $chunk) {
                $result = $indexer->getProductIds(array_keys($chunk));
                foreach ($result as $row) {
                    if ($row['type_id'] == 'configurable') {
                        continue;
                    }
                    $vendorSku = strtoupper($row['vendor_sku']);
                    if (!isset($chunk[$vendorSku])) {
                        Mage::helper('orderstat')->log('Error in row: ' . var_export($row, true), true);
                        continue;
                    }
                    $qty = (int)$chunk[$vendorSku];
                    $stockData[] = array(
                        'product_id'    => (int)$row['product_id'],
                        'stock_id'      => $stockId,
                        'qty'           => (int)$qty,
                        'is_in_stock'   => ($qty > 0 ? 1 : 0),
                    );
                }
            }

            $stockTable = Mage::getResourceModel('cataloginventory/stock_item')->getMainTable();
            /** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
            $adapter = Mage::getSingleton('core/resource')->getConnection('catalog_write');
            $adapter->beginTransaction();
            try {
                // reset stock
                $this->_setOutOfStock();

                foreach (array_chunk($stockData, 5000) as $data) {
                    $adapter->insertOnDuplicate($stockTable, $data, array('qty', 'is_in_stock'));
                }

                $adapter->commit();
                Mage::helper('orderstat')->log('Imported ' . count($stockData) . ' products');
            } catch (Exception $e) {
                $adapter->rollback();
                Mage::logException($e);
                Mage::helper('orderstat')->log('Error updating stock: ' . $e->getMessage(), true);
            }
        }
    }

    public function getLastSyncDate()
    {
        if (!$this->_lastSyncDate) {
            $date =  $this->getHelper()->getModuleConfig($this->_configPath . '/lastSync');

            if (!$date) {
                $date = Mage::getSingleton('core/date')->gmtTimestamp();
                $date -= 60*60*24; // sub 1 day
            }

            $this->_lastSyncDate = $date;

        }

        return $this->_lastSyncDate;
    }

    /**
     * Sets all products out of stock, during import rest will be set in stock
     */
    protected function _setOutOfStock()
    {
        $stockCollection = Mage::getResourceModel('cataloginventory/stock_item_collection');
        $adapter = $stockCollection->getConnection();

        // set all out of stock
        $adapter->update(
            $stockCollection->getMainTable(),
            array('is_in_stock' => 0)
        );

        // set configurables in stock (they will be set as out of stock by indexer)
        $select  = $adapter->select()
            ->join(
                array('e' => $stockCollection->getTable('catalog/product')),
                'e.entity_id = si.product_id',
                array())
            ->where('e.type_id = ?', 'configurable')
            ->columns(array(
                'is_in_stock' => new Zend_Db_Expr('1'),
            ));
        $query   = $adapter->updateFromSelect($select, array('si' => $stockCollection->getTable('cataloginventory/stock_item')));
        $adapter->query($query);
    }

    /**
     * Sets Last Sync Date
     *
     * @param $date
     */
    public function setLastSyncDate($date)
    {
        $this->_lastSyncDate = $date;
    }

    /**
     * Save Last Sync Date
     *
     * @param int $lastSync
     */
    public function saveLastSyncDate($lastSync)
    {
        Mage::getConfig()->saveConfig(
            Oro_Orderstat_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastSync',
            $lastSync
        );

        Mage::app()->getCache()->clean();
    }
}