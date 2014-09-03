<?php

/**
 * @category   Oro
 * @package    Oro_Pricemultiplier
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Pricemultiplier_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PRICE_MULTIPLIER = 'pricemultiplier/settings/price_multiplier';

    /**
     * @param int|string|Mage_Core_Model_Store|null $store
     * @return boolean
     */
    public function getPriceMultiplier($store = null)
    {
    	$multiplier = (float) Mage::getStoreConfig(self::XML_PATH_PRICE_MULTIPLIER, $store);
    	// fall back to 1 if the value set isn't a number
    	if(!is_numeric($multiplier)){
    		$multiplier = 1;
    	}
      return $multiplier;
   	}

}
