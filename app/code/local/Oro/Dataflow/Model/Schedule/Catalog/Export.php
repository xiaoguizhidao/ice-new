<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Schedule_Catalog_Export
    extends Oro_Dataflow_Model_Schedule_Abstract
{
    /**
     * Define Resource Model
     */
    public function _construct()
    {
        $this->_init('oro_dataflow/schedule_catalog_export');
    }

    /**
     * @return $this
     */
    public function execute()
    {
        try {
            $this->_getProfile()->setData('schedule', $this);

            switch ($this->getData('status')) {
                case Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING:
                case Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND:
                    $this->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_IN_PROCESS);
                    $this->save();
                    $this->_run();
                    break;
            }
        } catch (Exception $e) {
            $this->_log($e->getMessage());
            $this->addData(
                array(
                    'executed_at' => Mage::getModel('core/date')->gmtDate(),
                    'status' => Oro_Dataflow_Model_Schedule_Interface::STATUS_FAILED,
                )
            );
            $this->_clearBatchData();
            $this->save();
        }

        return $this;
    }

    /**
     * Execute export
     */
    public function _run()
    {
        if ($this->getData('batch_id')) {
            Mage::getSingleton('dataflow/batch')->load($this->getData('batch_id'));
        }

        $this->_getProfile()->run();
        $this->_log($this->_getProfile()->getExceptions());

        if ($this->getData('rows_total')) {
            $this->setData('progress', $this->getData('rows_complete')/$this->getData('rows_total') * 100);
        }

        $this->_log(Mage::helper('oro_dataflow')->__('Rows complete: %d of %d',
            $this->getData('rows_complete'),
            $this->getData('rows_total')));

        if ($this->getData('rows_complete') >= $this->getData('rows_total')) {
            $status = Oro_Dataflow_Model_Schedule_Interface::STATUS_SUCCESS;
        } else {
            $status = Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND;
        }

        $this->addData(
            array(
                'executed_at' => Mage::getModel('core/date')->gmtDate(),
                'status' => $status,
                'batch_id' => $this->_getProfile()->getData('batch_id'),
            )
        );

        if (Oro_Dataflow_Model_Schedule_Interface::STATUS_SUCCESS == $status) {
            $this->_clearBatchData();
        }

        $this->save();

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return Mage::helper('oro_dataflow')->getExportProfileId();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->isObjectNew()) {
            $this->setData('batch_size', Mage::helper('oro_dataflow')->getExportBatchSize());
        }

        return parent::_beforeSave();
    }
}
