<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Resource_Schedule_Catalog_Export
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Defines Resource Table and Unique Fields
     */
    public function _construct()
    {
        $this->_init('oro_dataflow/schedule_export', 'id');
    }

}
