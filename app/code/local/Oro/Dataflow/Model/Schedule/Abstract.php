<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
abstract class Oro_Dataflow_Model_Schedule_Abstract
    extends Mage_Core_Model_Abstract
    implements Oro_Dataflow_Model_Schedule_Interface
{
    /** @var  Mage_Dataflow_Model_Profile */
    protected $_profile;
    /** @var  array */
    protected $_log;

    /**
     * @return $this
     */
    protected function _clearBatchData()
    {
        if ($this->getData('batch_id')) {
            Mage::getSingleton('dataflow/batch')->load($this->getData('batch_id'))->delete();
            $this->unsetData('batch_id');
        }

        return $this;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->getData('scheduled_at_date') && $this->getData('scheduled_at_time')) {
            $time = $this->getData('scheduled_at_time');
            $localeScheduledAt = $this->getData('scheduled_at_date') . " {$time[0]}:{$time[1]}:$time[2]";
            $this->setData('scheduled_at', Mage::getModel('core/date')->gmtDate(null,strtotime($localeScheduledAt)));
        }

        if ($this->isObjectNew()) {
            $this->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING);
        }

        if (is_array($this->getData('ids'))) {
            $this->setData('ids', Mage::helper('core')->jsonEncode($this->getData('ids')));
        }

        if (is_array($this->_log)) {
            $this->setData('log', Mage::helper('core')->jsonEncode($this->_log));
        }

        return parent::_beforeSave();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        if ($this->getData('ids')) {
            $this->setData('ids', Mage::helper('core')->jsonDecode($this->getData('ids')));
        }

        return parent::_afterLoad();
    }

    /**
     * @return Mage_Dataflow_Model_Profile
     */
    protected function _getProfile()
    {
        if (is_null($this->_profile)) {
            $this->_profile = Mage::getModel('dataflow/profile')->load($this->getProfileId());
        }

        return $this->_profile;
    }

    /**
     * @param $msg
     * @return $this
     */
    public function log($msg)
    {
        $this->_log($msg);

        return $this;
    }

    /**
     * @param $msg
     * @return $this
     */
    protected function _log($msg)
    {
        if (is_null($this->_log)) {
            $this->_log = $this->getLog();
        }

        if (is_array($msg)) {
            foreach ($msg as $m) {
                $this->_log[] = (string) ($m instanceof Exception ? $m->getMessage() : $m);
            }
        } else {
            $this->_log[] = (string) ($msg instanceof Exception ? $msg->getMessage() : $msg);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLog()
    {
        if (is_null($this->_log)) {
            if ($this->getData('log')) {
                $this->_log = Mage::helper('core')->jsonDecode($this->getData('log'));
            } else {
                $this->_log = array();
            }
        }

        return $this->_log;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        if (!is_array($this->getData('ids'))) {
            $ids = Mage::helper('core')->jsonDecode($this->getData('ids'));
            if (is_array($ids)) {
                $this->setData('ids', $ids);
            } else {
                $this->setData('ids', array());
            }
        }

        return $this->getData('ids');
    }

    /**
     * @return int
     */
    abstract public function getProfileId();
}
