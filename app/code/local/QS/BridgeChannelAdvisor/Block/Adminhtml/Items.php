<?php
/**
 * Adminhtml ChannelAdvisor Content Items Grids Container
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('bridgechanneladvisor/items.phtml');
    }

    /**
     * Preparing layout
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items
     */
    protected function _prepareLayout()
    {
        $this->setChild('item', $this->getLayout()->createBlock('bridgechanneladvisor/adminhtml_items_item'));
        $this->setChild('product', $this->getLayout()->createBlock('bridgechanneladvisor/adminhtml_items_product'));
        $this->setChild('store_switcher', $this->getLayout()->createBlock('bridgechanneladvisor/adminhtml_store_switcher'));
        return $this;
    }

    /**
     * Get HTML code for Store Switcher select
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Get selecetd store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_getData('store');
    }

    /**
     * Check whether synchronization process is running
     *
     * @return bool
     */
    public function isProcessRunning()
    {
        $expProducts = Mage::getModel('bridgechanneladvisor/settings')->load(2);
        return $expProducts->getChannelFlag();
    }

    /**
     * Build url for retrieving background process status
     *
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->getUrl('*/*/status');
    }
}
