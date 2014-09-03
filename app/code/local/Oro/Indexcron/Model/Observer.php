<?php
/**
 * @category   Oro
 * @package    Oro_Indexcron
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Observer Class
 */
class Oro_Indexcron_Model_Observer
{

    /**
     * Update selected indexes
     */
    public function reindex()
    {
        /** @var Oro_Indexcron_Helper_Data $helper */
        $helper = Mage::helper('indexcron');
        if (!$helper->isActive()) {
            return;
        }

        foreach ($helper->getIndexes() as $indexer_code) {
            $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode($indexer_code);
            if ($indexProcess) {
                Mage::log('Reindex started: ' . $indexer_code);
                $indexProcess->reindexAll();
                Mage::log('Reindex end: ' . $indexer_code);
            }
        }
    }

} 
