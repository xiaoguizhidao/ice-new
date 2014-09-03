<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Resource_Schedule_Catalog_Import
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Defines Resource Table and Unique Fields
     */
    public function _construct()
    {
        $this->_init('oro_dataflow/schedule_import', 'id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);

        $baseDir = Mage::getBaseDir();

        if ($object->getData('file_path')) {
            $filePath = $baseDir . DS . trim($object->getData('file_path'));
            if (is_file($filePath)) {
                unlink($filePath);
                rmdir(dirname($filePath));
            }
        }

        return $this;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param int $limit
     * @return array
     */
    public function getBatchImportIds(Mage_Core_Model_Abstract $object, $limit)
    {
        $connection = $this->_getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
        $select = $connection->select()
            ->from($this->getTable('dataflow/batch_import'), 'batch_import_id')
            ->where("batch_id = {$object->getData('batch_id')}")
            ->limit($limit)
        ;

        return $connection->fetchCol($select);
    }


    public function clearBatchImportIds($ids)
    {
        $connection = $this->_getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
        $connection->delete($this->getTable('dataflow/batch_import'), 'batch_import_id IN ('.implode(',', $ids).')');
        ;

        return $this;
    }
}
