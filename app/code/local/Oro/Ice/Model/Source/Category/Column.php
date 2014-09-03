<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Source model to provide options for column number attribute
 */
class Oro_Ice_Model_Source_Category_Column extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    const COLUMN_NUMBER = 3;


    /**
     * Get All Options as option array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $helper = Mage::helper('ice');

        $options = array(
            array(
                'label' => $helper->__('-- Do not render in menu --'),
                'value' => null
            )
        );

        for ($i = 1; $i <= self::COLUMN_NUMBER; $i++) {
            $options[] = array(
                'label' => $helper->__('Column %s', $i),
                'value' => $i
            );
        }

        return $options;
    }

} 
