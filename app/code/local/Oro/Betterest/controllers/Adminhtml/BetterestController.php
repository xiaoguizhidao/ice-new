<?php
class Oro_Betterest_Adminhtml_BetterestController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
	{
		$this->_title($this->__('System'))
				 ->_title($this->__('Web Services'))
				 ->_title($this->__('BetterRest'));

		$this->loadLayout()->_setActiveMenu('system/services/betterest');
		$this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
		$this->_addBreadcrumb($this->__('Betterest'), $this->__('BetteRest'));
		$this->_addBreadcrumb($this->__('BetterRest'), $this->__('BetterRest'));
		$this->renderLayout();
	}

	public function saveAction()
	{
		$request = $this->getRequest();

		$session = $this->_getSession();

		foreach($this->getRequest()->getPost('shipping') as $ship){
			$shipMap = Mage::getModel('betterest/betterest');

			$shipMap->setInternalShippingMethod($ship['internal']);
			$shipMap->setExternalShippingMethod($ship['external']);
			$shipMap->save();
			$this->_redirect('*/*/index');
		}


	}
}