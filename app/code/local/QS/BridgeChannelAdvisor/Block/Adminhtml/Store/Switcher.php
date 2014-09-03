<?php
/**
 * Adminhtml BridgeChannelAdvisor Store Switcher
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{
    /**
     * Set overriden params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseConfirm(false)->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)));
    }
}
