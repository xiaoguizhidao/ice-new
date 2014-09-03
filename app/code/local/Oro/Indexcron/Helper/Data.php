<?php
/**
 * @category   Oro
 * @package    Oro_Indexcron
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */ 
class Oro_Indexcron_Helper_Data extends Mage_Core_Helper_Abstract
{

    const CONFIG_PATH_PATTERN = 'indexcron/general/%s';


    public function isActive()
    {
        return Mage::getStoreConfigFlag(sprintf(self::CONFIG_PATH_PATTERN, 'is_active'));
    }

    public function getIndexes()
    {
        return explode(',', Mage::getStoreConfig(sprintf(self::CONFIG_PATH_PATTERN, 'indexes')));
    }

}
