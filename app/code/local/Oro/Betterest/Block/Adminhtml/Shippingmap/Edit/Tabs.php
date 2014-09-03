<?php
class Oro_Betterest_Block_Adminhtml_Shippingmap_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("shippingmap_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("betterest")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("betterest")->__("Item Information"),
				"title" => Mage::helper("betterest")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("betterest/adminhtml_shippingmap_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
