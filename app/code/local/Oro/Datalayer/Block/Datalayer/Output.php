<?php

/**
 * @category   Oro
 * @package    Oro_Datalayer
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Datalayer_Block_Datalayer_Output extends Mage_Core_Block_Template
{

    /**
     * @return string
     */
    public function getDataLayerName()
    {
        return Mage::getStoreConfig('datalayer/settings/variable_name');
    }

    /**
     * @return string
     */
    public function getDataLayer()
    {
        return Mage::getSingleton('datalayer/datalayer')->toJSON();
    }

    /**
     * @return boolean
     */
    public function isDataLayerEnabled()
    {
        return Mage::getStoreConfig('datalayer/settings/is_enabled');
    }
}
