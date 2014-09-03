<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class to work with sftp and local files
 */
class Oro_Delmar_Model_Source_Ftptype
{

    /**
     * Returns options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => 'Secure FTP', 'value' => 'sftp'),
            array('label' => 'Regular FTP', 'value' => 'ftp')
        );
    }

    /**
     * Returns options as hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return array(
            'Secure FTP' => 'sftp',
            'Regular FTP' => 'ftp'
        );
    }
}
