<?php

class Oro_Ice_Helper_Affirmpayments extends Mage_Core_Helper_Abstract
{
    public function isAvailable(Mage_Sales_Model_Quote_Address $address)
    {
        $unAvail = $this->_getUnavailableLocations();
        $countryId = $address->getCountryId();
        $regionCode = $address->getRegionCode();
        if(!in_array($countryId, $unAvail['countries']) && (in_array($countryId, array('US')) && !in_array($regionCode, $unAvail['states']))){
            return true;
        }
        return false;

    }



    protected function _getUnavailableLocations()
    {

        $unavailable =  array(
            'countries' => array('CA'),
            'states' => array(
                'AL',
                'DE',
                'ID',
                'MD',
                'MS',
                'MO',
                'NV',
                'NM',
                'ND',
                'RI',
                'SD',
            )
        );

        return $unavailable;
    }
}