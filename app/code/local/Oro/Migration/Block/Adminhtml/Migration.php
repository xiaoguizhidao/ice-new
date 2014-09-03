<?php
/**
 * @category   Oro
 * @package    Oro_Migration
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Block to render script form
 */
class Oro_Migration_Block_Adminhtml_Migration extends Mage_Adminhtml_Block_Abstract
{


    /**
     * Gets current Magento Connection config
     *
     * @param string $field
     * @return string
     */
    public function getDbConfig($field)
    {
        $config = Mage::getConfig()->getResourceConnectionConfig("core_setup");

        return (string) $config->$field;
    }

    /**
     * Format Tables to increment as Json
     *
     * @return string
     */
    public function getTablesJson()
    {
        return Zend_Json::encode(Mage::helper('oro_migration')->getTablesToIncrement());
    }

    /**
     * Format Tables to increment as Json
     *
     * @return string
     */
    public function getRulesJson()
    {
        return Zend_Json::encode(Mage::helper('oro_migration')->getRulesToSync());
    }

    /**
     * Format Tables to increment as Json
     *
     * @return string
     */
    public function getLastIdJson()
    {
        $processor = Mage::getResourceModel('oro_migration/increment');
        $ids = array(
            'checked' => true,
            'data' => $processor->getLastIds()
        );

        return Zend_Json::encode($ids);
    }

}
