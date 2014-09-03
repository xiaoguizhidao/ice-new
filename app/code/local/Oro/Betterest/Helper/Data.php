<?php
class Oro_Betterest_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getShippingMap()
	{
		return Mage::getModel('betterest/shippingmap')->getCollection();
	}
}
	 