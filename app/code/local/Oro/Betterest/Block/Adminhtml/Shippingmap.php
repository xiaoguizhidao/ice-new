<?php


class Oro_Betterest_Block_Adminhtml_Shippingmap extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	public function __construct()
	{

	$this->_controller = "adminhtml_shippingmap";
	$this->_blockGroup = "betterest";
	$this->_headerText = Mage::helper("betterest")->__("Shippingmap Manager");
	$this->_addButtonLabel = Mage::helper("betterest")->__("Add New Item");
	parent::__construct();
	
	}

}