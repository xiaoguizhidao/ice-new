<?php

class Oro_Betterest_Model_Resource_Betterest extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('oro_betterest', 'id');
		$this->_storeId = (int)Mage::app()->getStore('ice_us')->getId();
	}
}