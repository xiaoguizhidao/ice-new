<?php

class Oro_Betterest_Adminhtml_ShippingmapController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("betterest/shippingmap")->_addBreadcrumb(Mage::helper("adminhtml")->__("Shippingmap  Manager"),Mage::helper("adminhtml")->__("Shipping Map Manager"));
				return $this;
		}
		public function indexAction() 
		{
			$this->_title($this->__("Betterest"));
			$this->_title($this->__("Betterest Shipping Map Manager"));

			$this->_initAction();
			$this->renderLayout();
		}

		public function editAction()
		{			    
			    $this->_title($this->__("Betterest"));
				$this->_title($this->__("Shippingmap"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("betterest/shippingmap")->load($id);
				if ($model->getId()) {
					Mage::register("shippingmap_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("betterest/shippingmap");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shippingmap Manager"), Mage::helper("adminhtml")->__("Shippingmap Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shippingmap Description"), Mage::helper("adminhtml")->__("Shippingmap Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("betterest/adminhtml_shippingmap_edit"))->_addLeft($this->getLayout()->createBlock("betterest/adminhtml_shippingmap_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("betterest")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Betterest"));
		$this->_title($this->__("Shippingmap"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("betterest/shippingmap")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("shippingmap_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("betterest/shippingmap");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shippingmap Manager"), Mage::helper("adminhtml")->__("Shippingmap Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shippingmap Description"), Mage::helper("adminhtml")->__("Shippingmap Description"));


		$this->_addContent($this->getLayout()->createBlock("betterest/adminhtml_shippingmap_edit"))->_addLeft($this->getLayout()->createBlock("betterest/adminhtml_shippingmap_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();

				if ($post_data) {

					try {

						$model = Mage::getModel("betterest/shippingmap")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Shippingmap was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setShippingmapData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setShippingmapData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("betterest/shippingmap");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
}
