<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Abstract class for import models
 */
abstract class Oro_Delmar_Model_Import_Abstract
{

    protected $_fileName = '_';
    protected $_configPath;
    protected $_lastSyncDate = null;

    /**
     * Updates orders with shipment info
     */
    public function update()
    {
        /** @var Oro_Delmar_Model_Io $ftp */
        $ftp = Mage::getModel('delmar/io')
            ->setTmpPath('Import')
            ->connect(Mage::helper('delmar')->getModuleConfig($this->_configPath . '/ftp_path'));

        $imported = $this->_batchImport($ftp, $this->getLastSyncDate());

        $ftp->close();

        if ($imported && Mage::helper('delmar')->getModuleConfig($this->_configPath . '/reindex', true)) {
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
     * Import single file
     * @param resource $file
     * @return mixed
     */
    abstract public function import($file);

    /**
     * Import batch of files
     *
     * @param Oro_Delmar_Model_Io $ftp
     * @param int $lastSync
     * @return int
     */
    abstract protected function _batchImport($ftp, $lastSync);

    /**
     * Get Date of last synchronization from config
     *
     * @return int
     */
    public function getLastSyncDate()
    {
        if (!$this->_lastSyncDate) {
            $date =  Mage::helper('delmar')->getModuleConfig($this->_configPath . '/lastSync');

            if (!$date) {
                $date = Mage::getSingleton('core/date')->gmtTimestamp();
                $date -= 60*60*24; // sub 1 day
            }

            $this->_lastSyncDate = $date;

        }

        return $this->_lastSyncDate;
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
            Oro_Delmar_Helper_Data::CONFIG_PATH . '/' . $this->_configPath . '/lastSync',
            $lastSync
        );

        Mage::app()->getCache()->clean();
    }

} 
