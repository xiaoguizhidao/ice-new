<?php
/**
 * @category   Oro
 * @package    Oro_Indexcron
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Indexcron_Model_Source_Index
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        foreach (Mage::getResourceModel('index/process_collection') as $item) {
            $options[] = array(
                'value' => $item->getIndexerCode(),
                'label' => Mage::helper('adminhtml')->__($item->getIndexer()->getName())
            );

        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();

        foreach (Mage::getResourceModel('index/process_collection') as $item) {
            $options[$item->getCode()] = Mage::helper('adminhtml')->__($item->getIndexer()->getName());
        }

        return $options;
    }
} 
