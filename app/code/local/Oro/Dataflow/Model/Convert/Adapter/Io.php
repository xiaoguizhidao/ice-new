<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Convert_Adapter_Io
    extends Mage_Dataflow_Model_Convert_Adapter_Io
{
    /**
     * @return string
     */
    protected function _getDataFilePath()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');

        return $batchModel->getIoAdapter()->getFile(true);
    }

    /**
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function load()
    {
        /** @var Mage_Dataflow_Model_Convert_Profile $profile */
        $profile  = $this->getProfile();
        $data     = $profile->getDataflowProfile();

        if (isset($data['schedule'])) {
            /** @var Oro_Dataflow_Model_Schedule_Abstract $schedule */
            $schedule = $data['schedule'];
            if ($schedule instanceof Oro_Dataflow_Model_Schedule_Catalog_Import && $this->getVar('type') == 'file') {
                $this->setVar('path', dirname($schedule->getData('file_path')));
                $this->setVar('filename', basename($schedule->getData('file_path')));
            }
        }

        return parent::load();

    }

    /**
     * Save result to destination file from temporary
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function save()
    {
        /** @var Mage_Dataflow_Model_Convert_Profile $profile */
        $profile  = $this->getProfile();
        $data     = $profile->getDataflowProfile();

        if (isset($data['schedule']) && $data['schedule'] instanceof Oro_Dataflow_Model_Schedule_Catalog_Export) {
            /** @var Oro_Dataflow_Model_Schedule_Abstract $schedule */
            $schedule = $data['schedule'];
            if ($schedule->getData('rows_complete') >= $schedule->getData('rows_total')) {
                parent::save();

                $scheduleDir = Mage::getBaseDir('var') . DS . 'export' . DS . 'schedule' . DS  .  $schedule->getId();
                $scheduleFile = $scheduleDir . DS . $this->getVar('filename');

                mkdir($scheduleDir, 0755, true);
                copy($this->_getDataFilePath(), $scheduleFile);

                $schedule->setData('file_path', $scheduleFile);
            }
        } else {
            parent::save();
        }

        return $this;
    }
}
