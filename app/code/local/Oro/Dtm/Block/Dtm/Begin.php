<?php

/**
 * @category   Oro
 * @package    Oro_DTM
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Dtm_Block_Dtm_Begin extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getDtmUrl()
    {
        if ($this->_isProduction()) {
            $dtmUrl = Mage::getStoreConfig('dtm/settings/production');
        } else {
            $dtmUrl = Mage::getStoreConfig('dtm/settings/staging');
        }

        return $dtmUrl;
    }

    /**
     * @return boolean
     */

    protected function _isProduction()
    {
        $prodSubDomains = Mage::getStoreConfig('dtm/settings/production_subdomains');

        $prodSubDomains = explode(',', $prodSubDomains);

        return in_array($this->_getSubdomain(), $prodSubDomains);
    }

    /**
     * @return string
     */

    protected function _getSubDomain()
    {
        $host = Mage::app()->getRequest()->getHttpHost();

        $hostParts = explode('.', $host);
        return $hostParts[0];
    }
}
