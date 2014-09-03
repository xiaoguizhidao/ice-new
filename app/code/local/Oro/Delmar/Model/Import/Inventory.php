<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class for updating inventory from remote FTP
 */
class Oro_Delmar_Model_Import_Inventory extends Oro_Delmar_Model_Import_Abstract
{
    const FIELD_VENDOR_SKU = 0;
    const FIELD_QTY = 1;

    protected $_fileName = 'ICEUSINVENTORY';
    protected $_configPath = Oro_Delmar_Helper_Data::CONFIG_PATH_INVENTORY;


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

            $vendorSku = strtoupper(trim($row[self::FIELD_VENDOR_SKU]));
            $originalData[$vendorSku] = (int)$row[self::FIELD_QTY];
        }

        $stockId   = (int)Mage::getSingleton('cataloginventory/stock')->getId();
        $stockData = array();
        $indexer = Mage::getModel('delmar/indexer_vendorsku');
        // convert SKU to ID
        foreach (array_chunk($originalData, 1000, true) as $chunk) {
            $result = $indexer->getProductIds(array_keys($chunk));
            foreach ($result as $row) {
                if ($row['type_id'] == 'configurable') {
                    continue;
                }
                $vendorSku = strtoupper($row['vendor_sku']);
                if (!isset($chunk[$vendorSku])) {
                    Mage::helper('delmar')->log('Error in row: ' . var_export($row, true), true);
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
            Mage::helper('delmar')->log('Imported ' . count($stockData) . ' products');
        } catch (Exception $e) {
            $adapter->rollback();
            Mage::logException($e);
            Mage::helper('delmar')->log('Error updating stock: ' . $e->getMessage(), true);
        }
	    }
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
     * Import batch of files
     *
     * @param Oro_Delmar_Model_Io $ftp
     * @param int $lastSync
     * @return int
     */
    protected function _batchImport($ftp, $lastSync)
    {
        $lastDate = 0;
        $imported = 0;
        $importFile = false;
        foreach ($ftp->ls('/^'.$this->_fileName.'(.{12})(|.{6})\.csv/') as $file) {
            $date = new Zend_Date($file['found'][1], 'YYYYMMddHHmm');
            $date = $date->getTimestamp();
            if ($date > $lastDate) {
                $lastDate = $date;
                $importFile = $file;
            }
        }

        if ($importFile && $lastDate > $lastSync) {
            Mage::helper('delmar')->log('Processing file:' . $file['text']);

            $ftp->read($file['text'], $ftp->tmpFile($this->_configPath, false));
            $this->import($ftp->tmpFile($this->_configPath, 'r'));
            $imported++;

            $this->setLastSyncDate($lastDate);
        }

        return $imported;
    }

}
