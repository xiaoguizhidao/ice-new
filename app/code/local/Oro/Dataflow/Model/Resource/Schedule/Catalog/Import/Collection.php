<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Resource_Schedule_Catalog_Import_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define Model for collection
     */
    public function _construct()
    {
        $this->_init('oro_dataflow/schedule_catalog_import');
    }
}
