<?php

Class Strands_Recommender_Helper_Data extends Strands_Recommender_Helper_Abstract
{

	
	/**
	 * @return the current page
	 */
	public function getCurrentPage()
	{
		if (!$this->hasData("current_page")) {
			$module 	= strtolower(Mage::app()->getRequest()->getModuleName());
			$controller	= strtolower(Mage::app()->getRequest()->getControllerName());
			$action		= strtolower(Mage::app()->getRequest()->getActionName());
		
			if (isset($action) && $action != '') $this->setCurrentPage($module.'_'.$controller.'_'.$action);
			elseif (isset($controller) && $controller != '') $this->setCurrentPage($module.'_'.$controller.'_index');
			else $this->setCurrentPage($module."_index_index");
			
			if ($this->getCurrentPage() == 'checkout_multishipping_success') $this->setCurrentPage('checkout_onepage_success');
		} return $this->getData('current_page');
	}
	
} 

?>