<?php

class Oro_Betterest_Block_Adminhtml_Shippingmap_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("shippingmapGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("betterest/shippingmap")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("betterest")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("internal_shipping_method", array(
				"header" => Mage::helper("betterest")->__("Internal Shipping Method"),
				"index" => "internal_shipping_method",
				));
				$this->addColumn("external_shipping_method", array(
				"header" => Mage::helper("betterest")->__("External Shipping Method"),
				"index" => "external_shipping_method",
				"row_field" => 'testing'
				));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		

}