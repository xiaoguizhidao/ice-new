<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class to work with sftp and local files
 */
class Oro_Orderstat_Model_Io
{

    protected $_tmpFilename = null;
    protected $_tmpFolder = null;
    /* Oro_Orderstat_Helper_[helper] */
    protected $_helper = null;
    /** @var Oro_Orderstat_Model_Resource_Io_Interface  */
    protected $_adapter = null;


    /**
     * Initializes ftp adapter
     */
    public function __construct()
    {
        $adapter = Mage::helper('orderstat')->getFtpConfig('adapter');
        $adapter = 'orderstat/io_' . strtolower($adapter);

        $this->_adapter = Mage::getResourceModel($adapter);
    }

    /**
     * @param Oro_Orderstat_Helper_[helper]
     */
    public function setHelper($helper)
    {
        $this->_helper = $helper;
        return $this;
    }
    public function getHelper()
    {
        /*
         * if no helper is set, use the default
         */
        if(!$this->_helper){
            $this->setHelper(Mage::helper('orderstat'));
        }

        return $this->_helper;
    }
    /**
     * Establish connection with SFTP server
     *
     * @param string $path
     * @return Oro_Orderstat_Model_Io
     */
    public function connect($path)
    {

        $_helper = $this->getHelper();
        $connData = array(
            'host' => $_helper->getFtpConfig('host'),
            'username' => $_helper->getFtpConfig('user'),
            'password' => $_helper->getFtpConfig('password'),
            'passive' => $_helper->getFtpConfig('passive', true),

        );
        $this->_adapter->open($connData);

        $basePath = $_helper->getFtpConfig('path');
        if ($basePath) {
            $this->_adapter->cd($basePath);
        }

        if (!$this->_adapter->cd($path)) {
            $this->_adapter->mkdir($path, 0777, false);
            $this->_adapter->cd($path);
        }

        return $this;
    }

    /**
     * Creates and opens temporary file
     *
     * @param string $baseName
     * @param bool|string $open
     * @return resource
     */
    public function tmpFile($baseName, $open = 'w')
    {
        $file = $this->getTmpPath() . DS . $this->getTmpFilename($baseName);

        return $open ? fopen($file, $open) : $file;
    }

    /**
     * Returns tmp file path
     *
     * @return string
     */
    public function getTmpPath()
    {
        if (!$this->_tmpFolder) {
            $this->setTmpPath('');
        }

        return $this->_tmpFolder;
    }

    public function setTmpPath($path)
    {
        $baseDir = Mage::getBaseDir('var') . DS . 'Orderstat';
        $this->_tmpFolder = $baseDir . DS . $path;

        if (!is_dir($baseDir)) {
            @mkdir($baseDir);
        }

        if (!is_dir($this->_tmpFolder)) {
            @mkdir($this->_tmpFolder);
        }

        return $this;
    }

    public function setTmpFileName($fileName)
    {
        $this->_tmpFilename = $fileName;

        return $this;
    }

    /**
     * Returns tmp file name
     *
     * @param string $filename
     * @return string
     */
    public function getTmpFilename($filename = 'tmp')
    {
        if (!$this->_tmpFilename) {
            //if (Mage::helper('orderstat')->getGeneralConfig('debug', true)) {
                $filename .= time();
            //}
            $filename .= '.csv';

            $this->_tmpFilename = $filename;
        }

        return $this->_tmpFilename;
    }

    /**
     * Sends temporary file to remote server
     *
     * @param string $destFileName
     * @return bool
     */
    public function send($destFileName)
    {
        return $this->_adapter->send(
            $destFileName,
            $this->getTmpPath() . DS . $this->getTmpFilename()
        );
    }

    /**
     * Gets list of cwd subdirectories and files
     *
     */
    public function ls($grep=null)
    {
        return $this->_adapter->ls($grep);
    }

    /**
     * Reads a file from ftp
     *
     * @param string $filename
     * @param null|string $dest
     * @return mixed
     */
    public function read($filename, $dest = null)
    {
        return $this->_adapter->read($filename, $dest);
    }

    /**
     * Close ftp connection
     *
     * @return mixed
     */
    public function close()
    {
        return $this->_adapter->close();
    }
}
