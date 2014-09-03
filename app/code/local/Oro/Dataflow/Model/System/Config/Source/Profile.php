<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Model_System_Config_Source_Profile
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Mage_Dataflow_Model_Resource_Profile_Collection $collection */
        $collection = Mage::getResourceModel('dataflow/profile_collection');
        $options = array();

        foreach ($collection as $profile) { /** @var Mage_Dataflow_Model_Profile $profile */
            $options[] = array('label' => $profile->getData('name'), 'value' => $profile->getId());
        }

        return $options;
    }
}
