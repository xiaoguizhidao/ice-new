<?php
/**
 * Adminhtml ChannelAdvisor PrepareItems Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Prepareitems extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'bridgechanneladvisor';
        $this->_controller = 'adminhtml_prepareitems';
        $this->_headerText = Mage::helper('bridgechanneladvisor')->__('Manage Items for Export to CA');
        parent::__construct();
        $this->_removeButton('add');
    }
}
