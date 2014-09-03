<?php
class QS_BridgeChannelAdvisor_Model_Carrier_Carate extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'channeladvisor_rate';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return false;

		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');

        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setMethod($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice($this->getConfigData('price'));
        $method->setCost($this->getConfigData('price'));
        $result->append($method);

        return $result;
    }
    public function getAllowedMethods()
    {
        return array('channeladvisor_rate'=>$this->getConfigData('name'));
    }
}