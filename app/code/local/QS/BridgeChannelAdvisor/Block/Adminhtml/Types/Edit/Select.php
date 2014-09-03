<?php
/**
 * Adminhtml ChannelAdvisor Content attributes select block
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Edit_Select extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('bridgechanneladvisor/types/edit/select.phtml');
    }

}
