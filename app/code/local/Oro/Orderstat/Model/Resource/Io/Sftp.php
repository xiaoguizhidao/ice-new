<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */

/**
 * Class to enhance varien_ftp
 */
class Oro_Orderstat_Model_Resource_Io_Sftp extends Varien_Io_Sftp implements Oro_Orderstat_Model_Resource_Io_Interface
{

    /**
     * Sends temporary file to remote server
     *
     * @param string $destFileName
     * @param string $srcFileName
     * @return bool
     */
    public function send($destFileName, $srcFileName)
    {
        return $this->_connection->put(
            $destFileName,
            $srcFileName,
            NET_SFTP_LOCAL_FILE
        );
    }

    /**
     * Get list of cwd subdirectories and files
     *
     * @param string|null $grep
     * @return array
     */
    public function ls($grep=null)
    {
        $list = $this->_connection->nlist();
        $pwd = $this->pwd();
        $result = array();
        foreach($list as $name) {
            $found = array();
            if (!$grep || preg_match($grep, $name, $found)) {
                $result[] = array(
                    'text'  => $name,
                    'id'    => "{$pwd}{$name}",
                    'found' => $found
                );
            }
        }

        return $result;
    }
}
