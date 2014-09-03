<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Model_Observer
{
    const REGISTRY_CURRENT_SCHEDULE_ID    = 'oro_dataflow_current_schedule_id';
    const REGISTRY_CURRENT_SCHEDULE_CLASS = 'oro_dataflow_current_schedule_class';

    public function __construct()
    {
        register_shutdown_function(array($this, 'onShutdownLog'));
    }

    /**
     * @return $this
     */
    public function runSchedule()
    {
        if (Mage::helper('oro_dataflow')->isEnabled()) {
            $this->_runExportProfiles();
            $this->_runImportProfiles();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _runExportProfiles()
    {
        /** @var Oro_Dataflow_Model_Resource_Schedule_Catalog_Export_Collection $scheduleCollection */
        $scheduleCollection = Mage::getResourceModel('oro_dataflow/schedule_catalog_export_collection');
        $scheduleCollection->addFieldToFilter('status', array('in' => array(
            Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING,
            Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND,
        )));
        $scheduleCollection->addFieldToFilter('scheduled_at', array('lt' => Mage::getSingleton('core/date')->gmtDate()));

        foreach ($scheduleCollection as $schedule) { /** @var Oro_Dataflow_Model_Schedule_Catalog_Export $schedule */
            $schedule->execute();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _runImportProfiles()
    {
        /** @var Oro_Dataflow_Model_Resource_Schedule_Catalog_Import_Collection $scheduleCollection */
        $scheduleCollection = Mage::getResourceModel('oro_dataflow/schedule_catalog_import_collection');
        $scheduleCollection->addFieldToFilter('status', array('in' => array(
            Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING,
            Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND,
        )));
        $scheduleCollection->addFieldToFilter('scheduled_at', array('lt' => Mage::getSingleton('core/date')->gmtDate()));

        foreach ($scheduleCollection as $schedule) { /** @var Oro_Dataflow_Model_Schedule_Catalog_Import $schedule */
            Mage::unregister(self::REGISTRY_CURRENT_SCHEDULE_ID);
            Mage::unregister(self::REGISTRY_CURRENT_SCHEDULE_CLASS);
            Mage::register(self::REGISTRY_CURRENT_SCHEDULE_ID, $schedule->getId());
            Mage::register(self::REGISTRY_CURRENT_SCHEDULE_CLASS, get_class($schedule));
            $schedule->execute();
        }

        Mage::unregister(self::REGISTRY_CURRENT_SCHEDULE_ID);
        Mage::unregister(self::REGISTRY_CURRENT_SCHEDULE_CLASS);

        return $this;
    }

    /**
     * Called on shutdown for caching errors
     */
    public function onShutdownLog()
    {
        $error = error_get_last();

        $id   = Mage::registry(self::REGISTRY_CURRENT_SCHEDULE_ID);
        $model = Mage::registry(self::REGISTRY_CURRENT_SCHEDULE_CLASS);

        if ($id && $model) {
            if($error['type'] == E_ERROR
            || $error['type'] == E_PARSE
            || $error['type'] == E_COMPILE_ERROR
            || $error['type'] == E_CORE_ERROR)
            {
                $schedule = new $model();
                $schedule->load($id);
                if ($schedule instanceof Oro_Dataflow_Model_Schedule_Abstract) {
                    $schedule->setData('status', Oro_Dataflow_Model_Schedule_Interface::STATUS_FAILED);
                    $schedule->log('******************* FATAL ERROR *********************');
                    $schedule->log('MESSAGE: ' . $error['message']);
                    $schedule->log('FILE: ' . $error['file']);
                    $schedule->log('LINE: ' . $error['line']);
                    $schedule->log('******************* FATAL ERROR *********************');
                    $schedule->save();
                }
            }
        }
    }
}
