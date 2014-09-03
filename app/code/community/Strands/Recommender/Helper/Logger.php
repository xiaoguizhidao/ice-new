<?php

Class Strands_Recommender_Helper_Logger extends Strands_Recommender_Helper_Data
{
	const PREFIX = "Strands ";
	const ERROR1 = "Error: Insufficient permissions. File/directory is not writeable";
	
	protected $_log = "recommender.log";
	protected $_logHandler = null;

	
	/**
	 * Method to log error messages
	 * 
	 * @param $message is error message we are seeking to log
	 * @return boolean, true on success
	 */
	public function log($message)
	{	
		if ($this->_logHandler === null) {
			$folder = Mage::getConfig()->getVarDir() . "/log";
			
			if (!file_exists($folder)) {
				if (!@mkdir($folder)) {
					echo self::PREFIX . self::ERROR1 . "\r\n";
					return false;
				}	
			}
			
			if (!$this->_logHandler = @fopen($folder . DS . $this->_log, 'a')) {
				echo self::PREFIX . self::ERROR1 . "\r\n";
				return false;
			}
			
			
		}
		
		fwrite($this->_logHandler, self::PREFIX . " [" . date("mdY h:i:s") ."]: " . $message . "\r\n");
		return true;
	}
	
	public function __destruct() 
	{
		fclose($this->_logHandler);	
	}
}

?>