<?php

/**
 * @category   Oro
 * @package    Oro_DTM
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Dtm_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_IS_ENABLED = 'dtm/settings/is_enabled';

    /**
     * @param int|string|Mage_Core_Model_Store|null $store
     * @return boolean
     */
    public function isDtmEnabled($store = null)
    {

        return Mage::getStoreConfigFlag(self::XML_PATH_IS_ENABLED, $store);

    }

}
