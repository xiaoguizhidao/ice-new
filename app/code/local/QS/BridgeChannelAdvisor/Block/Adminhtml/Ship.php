<?php
/**
 * Adminhtml ChannelAdvisor Ship Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Ship extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'bridgechanneladvisor';
        $this->_controller = 'adminhtml_ship';
        $this->_addButtonLabel = Mage::helper('bridgechanneladvisor')->__('Add Shipment Mapping');
        $this->_headerText = Mage::helper('bridgechanneladvisor')->__('Manage Shipment Mapping');
        parent::__construct();
    }
}
