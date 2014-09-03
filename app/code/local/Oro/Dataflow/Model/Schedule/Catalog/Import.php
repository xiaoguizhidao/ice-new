<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Schedule_Catalog_Import
    extends Oro_Dataflow_Model_Schedule_Abstract
{
    /**
     * Define Resource Model
     */
    public function _construct()
    {
        $this->_init('oro_dataflow/schedule_catalog_import');
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $this->_getProfile()->setData('schedule', $this);

        switch ($this->getData('status')) {
            case Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING:
                $this->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_IN_PROCESS);
                $this->save();

                $this->_run();
                $this->_processBatch();
                break;
            case Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND:
                $this->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_IN_PROCESS);
                $this->save();

                $this->_processBatch();
                break;
        }

        return $this;
    }

    /**
     * Execute export
     */
    protected function _run()
    {
        try {
            $this->_getProfile()->run();
            $this->_log($this->_getProfile()->getExceptions());
            $this->addData(array('batch_id' => $this->_getProfile()->getData('batch_id')));
            /** @var MCM $batchImportModel */
            $batchImportModel = Mage::getSingleton('dataflow/batch')->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();
            $this->setData('rows_total', count($importIds));
            $this->setData('executed_at', Mage::getModel('core/date')->gmtDate());
            //$this->setData('ids', $importIds);

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
    }


    /**
     * @return $this
     */
    protected function _processBatch()
    {
        try {
            if ($this->getData('batch_id')) {
                /** @var Mage_Dataflow_Model_Batch $batchModel */
                $batchModel = Mage::getSingleton('dataflow/batch')->load($this->getData('batch_id'));
                /** @var Mage_Dataflow_Model_Batch_Import $batchImportModel */
                $batchImportModel = $batchModel->getBatchImportModel();
                $adapter = Mage::getModel($batchModel->getAdapter());
                if (!$adapter) {
                    Mage::throwException(Mage::helper('oro_dataflow')->__('Adapter not found or contain errors.'));
                }
                $adapter->setBatchParams($batchModel->getParams());

                $batchSize = $this->getData('batch_size');


                $rowIds = $this->getResource()->getBatchImportIds($this, $batchSize);

                //$rowIds = array_slice($ids, 0, $batchSize);
                //$remainingIds = array_slice($ids, $batchSize);

                $saved = 0;
                $rowId = $this->getData('rows_complete');
                foreach ($rowIds as $importId) {
                    $rowId++;
                    $batchImportModel->load($importId);
                    if (!$batchImportModel->getId()) {
                        $this->_log(Mage::helper('dataflow')->__('Skip undefined row.'));
                        continue;
                    }

                    try {
                        $importData = $batchImportModel->getBatchData();
                        $adapter->saveRow($importData);
                    } catch (Exception $e) {
                        $this->_log("#{$rowId}: " . $e->getMessage());
                        continue;
                    }
                    $saved ++;
                }

                $this->getResource()->clearBatchImportIds($rowIds);

                $this->setData('rows_complete', count($rowIds) + (int) $this->getData('rows_complete'));

                $this->_log(Mage::helper('oro_dataflow')->__('Rows complete: %d of %d',
                    $this->getData('rows_complete'),
                    $this->getData('rows_total')));

                $remainingIds = $this->getResource()->getBatchImportIds($this, $batchSize);

                if (empty($remainingIds)) {
                    $this->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_SUCCESS);
                } else {
                    $this->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND);
                }
            }
        } catch (Exception $e) {
            $this->_log($e->getMessage());
            $this->addData(
                array(
                    'executed_at' => Mage::getModel('core/date')->gmtDate(),
                    'status' => Oro_Dataflow_Model_Schedule_Interface::STATUS_FAILED,
                )
            );
        }

        $this->save();

        return $this;
    }


    /**
     * @return int
     */
    public function getProfileId()
    {
        return Mage::helper('oro_dataflow')->getImportProfileId();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->isObjectNew()) {
            $this->setData('batch_size', Mage::helper('oro_dataflow')->getImportBatchSize());
        }

        return parent::_beforeSave();
    }
}
