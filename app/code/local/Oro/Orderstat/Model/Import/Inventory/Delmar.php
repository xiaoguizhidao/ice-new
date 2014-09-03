<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */

/**
 * Class for updating inventory from remote FTP
 */
class Oro_Orderstat_Model_Import_Inventory_Delmar extends Oro_Orderstat_Model_Import_Inventory_Abstract
{
    const FIELD_VENDOR_SKU = 0;
    const FIELD_QTY = 1;

    protected $_fileName = 'ICEUSINVENTORY';
    protected $_configPath = Oro_Orderstat_Helper_Delmar::CONFIG_PATH_INVENTORY;
    protected $_helper;
    protected $_lastSyncDate;

    public function getHelper()
    {
        if(!$this->_helper){
            $this->_helper = Mage::helper('orderstat/delmar');
        }
        return $this->_helper;
    }

    public function update()
    {
        /** @var Oro_Delmar_Model_Io $ftp */
        $ftp = Mage::getModel('orderstat/io')
            ->setHelper($this->getHelper())
            ->setTmpPath('Import')
            ->connect($this->getHelper()->getModuleConfig($this->_configPath . '/ftp_path'));

        $imported = $this->_batchImport($ftp, $this->getLastSyncDate());

        $ftp->close();

        if ($imported && $this->getHelper()->getModuleConfig($this->_configPath . '/reindex', true)) {
            $indexes = array('cataloginventory_stock', 'catalog_product_price');
            foreach ($indexes as $index) {
                $process = Mage::getModel('index/indexer')->getProcessByCode($index);
                $process->reindexAll();
            }

            Mage::getSingleton('varnishcache/control')->clean(Mage::helper('varnishcache/cache')->getStoreDomainList());
        }

        if ($imported) {
            $this->saveLastSyncDate($this->getLastSyncDate());
        }
    }

    /**
     * Import batch of files
     *
     * @param Oro_Orderstat_Model_Io $ftp
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
            Mage::helper('orderstat')->log('Processing file:' . $file['text']);

            $ftp->read($file['text'], $ftp->tmpFile($this->_configPath, false));
            $this->import($ftp->tmpFile($this->_configPath, 'r'));
            $imported++;

            $this->setLastSyncDate($lastDate);
        }

        return $imported;
    }

}
