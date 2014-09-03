<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */

/**
 * Class to enhance varien_ftp
 */
class Oro_Orderstat_Model_Resource_Io_Ftp extends Varien_Io_Ftp implements Oro_Orderstat_Model_Resource_Io_Interface
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
        return @ftp_put($this->_conn, $destFileName, $srcFileName, $this->_config['file_mode']);
    }

    /**
     * Get list of cwd subdirectories and files
     *
     * @param string|null $grep
     * @return array
     */
    public function ls($grep=null)
    {
        $ls = @ftp_nlist($this->_conn, '.');

        if (is_array($ls) && count($ls) == 0) {
            $ls = @ftp_nlist($this->_conn, './');
        }

        $list = array();
        if ($ls) {
            foreach ($ls as $file) {
                $found = array();
                if (!$grep || preg_match($grep, $file, $found)) {
                    $list[] = array(
                        'text'  => $file,
                        'id'    => $this->pwd().'/'.$file,
                        'found' => $found
                    );
                }
            }
        }

        return $list;
    }

    /**
     * Open ftp connection
     *
     * @param array $args
     * @return bool|void
     */
    public function open(array $args=array())
    {
        if (isset($args['username']) && !isset($args['user'])) {
            $args['user'] = $args['username'];
        }

        return parent::open($args);
    }
}
