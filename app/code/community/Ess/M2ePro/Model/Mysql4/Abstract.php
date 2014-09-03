<?php

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Mysql4_Abstract
    extends Mage_Core_Model_Mysql4_Abstract
{
    // ########################################

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $origData = $object->getOrigData();

        if (empty($origData)) {
            $object->setData('create_date',Mage::helper('M2ePro')->getCurrentGmtDate());
        }

        $object->setData('update_date',Mage::helper('M2ePro')->getCurrentGmtDate());

        return $this;
    }

    // ########################################
}