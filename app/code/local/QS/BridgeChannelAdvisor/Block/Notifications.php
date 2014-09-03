<?php
class QS_BridgeChannelAdvisor_Block_Notifications extends Mage_Core_Block_Template
{
    public  function getReq() {
        /** @var QS_BridgeChannelAdvisor_Helper_Data  $helper */
        $helper = Mage::helper('bridgechanneladvisor');
        $req = array();
        $req['max_execution_time'] = $helper->_getRequirements('max_execution_time');
        $req['memory_limit']  = $helper->_getRequirements('memory_limit');
        $req['max_allowed_packet']  = $helper->_getRequirements('max_allowed_packet');


        return $req;
    }

    public function getNotifications() {
        $req = $this->getReq();
        /** @var QS_BridgeChannelAdvisor_Helper_Data  $helper */
        $helper = Mage::helper('bridgechanneladvisor');

        return $helper->showErrorMessages($req['max_execution_time'], $req['memory_limit'], $req['max_allowed_packet'] );
    }

}