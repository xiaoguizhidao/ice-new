<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Block_Adminhtml_Catalog_Schedule_Details_Export
    extends Mage_Adminhtml_Block_Abstract
{
    /**
     * @return Oro_Dataflow_Model_Schedule_Abstract
     */
    public function getSchedule()
    {
        return Mage::registry('schedule');
    }
}
