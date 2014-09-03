<?php
/**
 * @category   Oro
 * @package    Oro_Diamondcandles
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */

class Oro_Diamondcandles_Model_Observer
{
    const XML_PATH_IS_ENABLED = 'diamondcandles/general/is_active';

    public function orderImport()
    {
        if ((bool) Mage::getStoreConfigFlag(self::XML_PATH_IS_ENABLED)) {
            $importer = Mage::getModel('diamondcandles/import');
            $importer->import();
        }
    }

}