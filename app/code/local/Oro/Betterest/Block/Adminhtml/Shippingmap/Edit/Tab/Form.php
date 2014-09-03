<?php
class Oro_Betterest_Block_Adminhtml_Shippingmap_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("betterest_form", array("legend"=>Mage::helper("betterest")->__("Item information")));

		$fieldset->addField("external_shipping_method", "text", array(
			"label" => Mage::helper("betterest")->__("External Shipping Method"),
			"name" => "external_shipping_method",
		));
	
		$fieldset->addField("internal_shipping_method", "select", array(
			"label" => Mage::helper("betterest")->__("Internal Shipping Method"),
			"name" => "internal_shipping_method",
			"values" => $this->_getShippingMethods()
		));

		if (Mage::getSingleton("adminhtml/session")->getShippingmapData()){
			$form->setValues(Mage::getSingleton("adminhtml/session")->getShippingmapData());
			Mage::getSingleton("adminhtml/session")->setShippingmapData(null);
		
		}elseif(Mage::registry("shippingmap_data")) {
				$form->setValues(Mage::registry("shippingmap_data")->getData());
		}
		return parent::_prepareForm();
	}



	protected function _getShippingMethods()
	{
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
	 
			$query = 'SELECT * FROM ' . $resource->getTableName('shipping_matrixrate');
	
			$results = $readConnection->fetchAll($query);
			$options = array();
			foreach($results as $result){
					$options['matrixrate_matrixrate_'. $result['pk']] = $result['delivery_type'];
			}
			return $options;
	}
}
