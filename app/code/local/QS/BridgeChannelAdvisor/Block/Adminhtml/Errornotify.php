<?php
/**
 * Adminhtml ChannelAdvisor Error Notify Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Errornotify extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'bridgechanneladvisor';
        $this->_controller = 'adminhtml_errornotify';
        $this->_headerText = Mage::helper('bridgechanneladvisor')->__('Manage Message');
        parent::__construct();
        $this->_removeButton('add');
    }

}
