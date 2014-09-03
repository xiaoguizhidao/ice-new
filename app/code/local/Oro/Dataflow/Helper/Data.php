<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SETTINGS_ENABLED        = 'oro_dataflow/import_export/enable';
    const XML_PATH_SETTINGS_EXPORT_SIZE    = 'oro_dataflow/import_export/export_task_size';
    const XML_PATH_SETTINGS_IMPORT_SIZE    = 'oro_dataflow/import_export/import_task_size';
    const XML_PATH_SETTINGS_ROOT_CATEGORY  = 'oro_dataflow/import_export/root_category';
    const XML_PATH_SETTINGS_EXPORT_PROFILE = 'oro_dataflow/import_export/export_dataflow_profile';
    const XML_PATH_SETTINGS_IMPORT_PROFILE = 'oro_dataflow/import_export/import_dataflow_profile';


    /**
     * @return array
     */
    public function getScheduleStatusOptionArray()
    {
        return array (
            //Oro_Dataflow_Model_Schedule_Interface::STATUS_CANCELED   => $this->__('Canceled'),
            Oro_Dataflow_Model_Schedule_Interface::STATUS_FAILED     => $this->__('Failed'),
            Oro_Dataflow_Model_Schedule_Interface::STATUS_IN_PROCESS => $this->__('In Process'),
            Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING    => $this->__('Pending'),
            Oro_Dataflow_Model_Schedule_Interface::STATUS_SUCCESS    => $this->__('Success'),
            Oro_Dataflow_Model_Schedule_Interface::STATUS_SUSPEND    => $this->__('Suspend'),
        );
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_SETTINGS_ENABLED);
    }

    /**
     * @return int
     */
    public function getRootCategoryId()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_SETTINGS_ROOT_CATEGORY);
    }

    /**
     * @return int
     */
    public function getExportBatchSize()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_SETTINGS_EXPORT_SIZE);
    }

    /**
     * @return int
     */
    public function getImportBatchSize()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_SETTINGS_IMPORT_SIZE);
    }

    /**
     * @return int
     */
    public function getExportProfileId()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_SETTINGS_EXPORT_PROFILE);
    }

    /**
     * @return int
     */
    public function getImportProfileId()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_SETTINGS_IMPORT_PROFILE);
    }
}
