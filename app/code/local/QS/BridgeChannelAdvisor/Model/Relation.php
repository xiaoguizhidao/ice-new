<?php
/**
 * ChannelAdvisor Relation Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Relation extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/relation");
    }

    public function getRelationsCollection()
    {
        return Mage::getModel('bridgechanneladvisor/relation')->getCollection();
    }

}
