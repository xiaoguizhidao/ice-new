<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Interface to make sure Io adapter has all necessary functions
 */
interface Oro_Orderstat_Model_Resource_Io_Interface extends Varien_Io_Interface
{

    /**
     * Sends temporary file to remote server
     *
     * @param string $destFileName
     * @param string $srcFileName
     * @return bool
     */
    public function send($destFileName, $srcFileName);

}
