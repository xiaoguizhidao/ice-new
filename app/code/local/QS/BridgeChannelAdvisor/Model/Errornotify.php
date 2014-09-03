<?php
/**
 * ChannelAdvisor Error Notify Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Errornotify extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/errornotify");
    }

    public function getErrornotifyCollection()
    {
        return Mage::getModel('bridgechanneladvisor/errornotify')->getCollection();
    }

    public function deleteAllData()
    {
        $coreResource = Mage::getSingleton('core/resource');
        $connection = $coreResource->getConnection('core_read');

        $truncate = $connection->truncate($coreResource->getTableName('bridgechanneladvisor/errornotify'));
        return $truncate;
    }
}
