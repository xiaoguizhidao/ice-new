<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Delmar_Model_System_Config_Backend_Timestamp extends Mage_Core_Model_Config_Data
{

    /**
     * Decrypt value after loading
     *
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value)) {
            $this->setValue(date('Y-m-d H:i', $value));
        }
    }

    /**
     * Encrypt value before saving
     *
     */
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();

        if (!empty($value)) {
            $this->setValue(strtotime($value));
        }
    }
}
