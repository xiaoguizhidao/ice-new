<?php
class QS_BridgeChannelAdvisor_Block_Adminhtml_Sales_Order_Shipment_View_Form extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form
{
    /**
     * Check is carrier has functionality of creation shipping labels
     *
     * @return boolean
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->getOrder()->getShippingCarrier();
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }
}
