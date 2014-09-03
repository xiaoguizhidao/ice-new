<?php
class QS_BridgeChannelAdvisor_Block_Adminhtml_Relationships extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'bridgechanneladvisor';
        $this->_controller = 'adminhtml_relationships';

        $this->_headerText = Mage::helper('bridgechanneladvisor')->__('Relationships');
        parent::__construct();
        $this->_removeButton('add');
    }
}