<?php


Class Strands_Recommender_IndexController extends Mage_Core_Controller_Front_Action
{
	const DEBUG = false;

	/**
	 * Gets the catalog in the response
	 */
	public function productlistrootAction() 
	{
		
		$model = Mage::getModel('recommender/abstract');
		$localfile = "strandsCatalog.xml";
		$absPath = Mage::getBaseDir();
		$localpath = $absPath."/media/";
		
		$params = $this->getRequest()->getParams();
		
		if (isset($params['maxcount']) && is_numeric($params['maxcount'])) {
			$maxcount = $params['maxcount'];
		} else {
			$maxcount = 0;
		}
		
		if (($maxcount > 0 ) && isset($params['offset']) && is_numeric($params['offset'])) {
			if (($offset = $params['offset']) == 0) {
				$page = 0;
			} else {
				$page = (int) ceil($offset/$maxcount) + 1;
			}
		} else {
			$page = 0;
		}
		
		if (isset($params['memory']) && ($params['memory'] == 'false' )) {
			$model->writeCatalog($localpath,$localfile,$page,$maxcount);
			
			$fh = fopen($localpath.$localfile,'r');
			$output = fread($fh,filesize($localpath.$localfile));
			fclose($fh);
		} else {
			$output = $model->writeCatalog($localpath,'memory',$page,$maxcount);
		}
		
		$this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')->setBody($output);
		
	}
	
	/**
	 * Gets the catalog in the response
	 */
	public function productlistAction() 
	{
		
		$catalogSelect = Mage::getStoreConfig('recommender/catalog/select');
		if (($catalogSelect == 1) || ($catalogSelect == 2)) {
			return;
		}
		
		$model = Mage::getModel('recommender/abstract');
		$localfile = "strandsCatalog.xml";
		$absPath = Mage::getBaseDir();
		$localpath = $absPath."/media/";
		
		$params = $this->getRequest()->getParams();
		
		if (!self::DEBUG) {
			if (($params['api_id'] != $model->getApiId()) || ($params['customer_id'] != $model->getCustomerToken())) {
				return;
			}
		}
		
		if (isset($params['maxcount']) && is_numeric($params['maxcount'])) {
			$maxcount = $params['maxcount'];
		} else {
			$maxcount = 0;
		}
		
		if (($maxcount > 0 ) && isset($params['offset']) && is_numeric($params['offset'])) {
			if (($offset = $params['offset']) == 0) {
				$page = 0;
			} else {
				$page = (int) ceil($offset/$maxcount) + 1;
			}
		} else {
			$page = 0;
		}
		
		
		if (isset($params['memory']) && ($params['memory'] == 'false' )) {
			$model->writeCatalog($localpath,$localfile,$page,$maxcount);
			
			$fh = fopen($localpath.$localfile,'r');
			$output = fread($fh,filesize($localpath.$localfile));
			fclose($fh);
		} else {
			$output = $model->writeCatalog($localpath,'memory',$page,$maxcount);
		}
		
		$this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')->setBody($output);

		Mage::getConfig()->saveConfig('recommender/catalog/strands_catalog_time',date('D, d M Y H:i:s' , Mage::getModel('core/date')->timestamp(time())));
		
	}
	

	
		
 	/**
 	 * Code borrowed from Mage_Catalog_Block_Product_View_Attributes
 	 * 
 	 * @return additional data for product
 	 */
    private function getAdditionalData($product, array $excludeAttr = array())
    {
    	if (!$product) return;
    	
        $data = array();
        //$product = $product;
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
//            if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
            if (!in_array($attribute->getAttributeCode(), $excludeAttr)) {

                $value = $attribute->getFrontend()->getValue($product);

                // TODO this is temporary skipping eco taxes
                if (is_string($value)) {
                    if (strlen($value) && $product->hasData($attribute->getAttributeCode())) {
                        if ($attribute->getFrontendInput() == 'price') {
                            $value = Mage::app()->getStore()->convertPrice($value,true);
                        } 
                        
                        //$value = htmlentities($value); // REMOVED
                        $data[$attribute->getAttributeCode()] = array(
                           'label' => $attribute->getStoreLabel(),
                           'value' => $value,
                           'code'  => $attribute->getAttributeCode()
                        );
                    }
                }
            }
        }
        return $data;
    }


    /**
     * 
     * Get a snapshot of the current status of the plugin.
     */
    public function getconfigAction() 
	{
		$xml = new DOMDocument('1.0','utf-8');
		$store = Mage::app()->getStore();
		
		$model = Mage::getModel('recommender/abstract');
		
		$params = $this->getRequest()->getParams();
		$errorNode = $xml->createElement('strands');
		
		if (!self::DEBUG) {
			if ($params['password'] !== 'strands') {
				$errorNode->appendChild($xml->createAttribute('error'))->appendChild($xml->createTextNode('-1'));
				$xml->appendChild($errorNode);
				return;
			}
		}
		
		$config = $xml->createElement('config');
		$xml->appendChild($config);
		$setup = $xml->createElement('setup');
		
		$setup->appendChild($xml->createElement('enabled'))->appendChild($xml->createTextNode($model->isActive() ? 'true' : 'false'));
		$setup->appendChild($xml->createElement('version'))->appendChild($xml->createTextNode($model->getVersion()));
		$setup->appendChild($xml->createElement('desired_catalog_upload'))->appendChild($xml->createTextNode($model->getDesiredCatalogTime() . 'h (GMT)'));
		$setup->appendChild($xml->createElement('last_catalog_upload'))->appendChild($xml->createTextNode(date("m-d-Y h:i:s",$model->getUploadCatalogNow())));
		$setup->appendChild($xml->createElement('login'))->appendChild($xml->createTextNode($model->getLogin()));
		
		$config->appendChild($setup);
		
		$modules = $xml->createElement('modules');
		
		foreach (Mage::getConfig()->getNode('modules')->children() as $mod) {
			$module = $xml->createElement('module');
			$module->appendChild($xml->createElement('name'))->appendChild($xml->createTextNode($mod->getName()));
			$module->appendChild($xml->createElement('version'))->appendChild($xml->createTextNode($mod->version));
			$module->appendChild($xml->createElement('codePool'))->appendChild($xml->createTextNode($mod->codePool));
			$module->appendChild($xml->createElement('active'))->appendChild($xml->createTextNode($mod->active));
			if ($mod->depends->hasChildren()) {
				$depends = $xml->createElement('depends');
				foreach($mod->depends->children() as $dep) {
					$depends->appendChild($xml->createElement('depend'))->appendChild($xml->createTextNode($dep->getName()));
				}
				$module->appendChild($depends);
			}
			$modules->appendChild($module);
		}
		
		$config->appendChild($modules);
		
		$output = $xml->saveXML();
		$this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')->setBody($output);	
	}
	
	
    public function getproductAction()
	{
		$xml = new DOMDocument('1.0','utf-8');
		$store = Mage::app()->getStore();
		
		$model = Mage::getModel('recommender/abstract');
		
		$params = $this->getRequest()->getParams();
		$errorNode = $xml->createElement('strands');
		
		if (!self::DEBUG) {
			if (($params['api_id'] != $model->getApiId()) || ($params['customer_id'] != $model->getCustomerToken())) {
				$errorNode->appendChild($xml->createAttribute('error'))->appendChild($xml->createTextNode('-1'));
				$xml->appendChild($errorNode);
				return;
			}
		}
		
		$collection = NULL;
		
		if (($params['id'] !== NULL)) {
			$product = Mage::getModel('catalog/product')->load($params['id']);
			var_dump($product);
		} else {
			
			$collection = Mage::getModel('catalog/product')->getCollection();
			
			if (($params['name'] !== NULL)) {
				$collection->addAttributeToSelect('name');
				$collection->addFieldToFilter(array(
					array('attribute'=>'name','eq'=>$params['name']),
				));
			}
			
			foreach ($collection as $product) {
				var_dump($product);
			}
		}
		
	}
	
	
	public function feedtestAction()
	{
		$xml = new DOMDocument('1.0','utf-8');
		
		$model = Mage::getModel('recommender/abstract');
		
		$params = $this->getRequest()->getParams();
		$errorNode = $xml->createElement('strands');
		
		if (!self::DEBUG) {
			if (($params['api_id'] != $model->getApiId()) || ($params['customer_id'] != $model->getCustomerToken())) {
				$errorNode->appendChild($xml->createAttribute('error'))->appendChild($xml->createTextNode('-1'));
				$xml->appendChild($errorNode);
				return;
			}
		}
		
		
		$setup = $xml->createElement('public_website');	
		$setup->appendChild($xml->createElement('success'))->appendChild($xml->createTextNode('true'));
		
		$xml->appendChild($setup);
		
		$output = $xml->saveXML();
		$this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')->setBody($output);	
		
	}
	
	
	public function crontestAction()
	{
		$xml = new DOMDocument('1.0', 'utf-8');
		
		$model = Mage::getModel('recommender/abstract');
		
		$params = $this->getRequest()->getParams();
		$errorNode = $xml->createElement('strands');
		
		if (!self::DEBUG) {
			if (($params['api_id'] != $model->getApiId()) || ($params['customer_id'] != $model->getCustomerToken())) {
				$errorNode->appendChild($xml->createAttribute('error'))->appendChild($xml->createTextNode('-1'));
				$xml->appendChild($errorNode);
				return;
			}
		}
		
		$answer = $xml->createElement('cron_history');
		
		$current_time = $xml->createElement('current_time');
		$answer->appendChild($current_time);
	
		$ct = time();
		
		$current_time->appendChild($xml->createElement('epoch'))->appendChild($xml->createTextNode($ct));
		$current_time->appendChild($xml->createElement('human'))->appendChild($xml->createTextNode(date('D, d M Y H:i:s' , Mage::getModel('core/date')->timestamp($ct))));
		
		$coreResource = Mage::getSingleton('core/resource');
		$read = $coreResource->getConnection('core_read');
		$cron_schedule = $coreResource->getTableName ('cron_schedule');
		$sql = "SELECT executed_at FROM $cron_schedule WHERE status='success' AND job_code='recommender_apply_all' ORDER BY finished_at DESC limit 1";
		$lcdb = $read->fetchAll($sql);

		if (count($lcdb)) {
			$lc = $lcdb[0]['executed_at'];
			$last_cron = $xml->createElement('last_cron');
			$answer->appendChild($last_cron);
			$last_cron->appendChild($xml->createElement('epoch'))->appendChild($xml->createTextNode(Mage::getModel('core/date')->timestamp($lc)));
			$last_cron->appendChild($xml->createElement('human'))->appendChild($xml->createTextNode(date('D, d M Y H:i:s' , Mage::getModel('core/date')->timestamp($lc))));
		} else {
			$no_cron = $xml->createElement('no_cron');
			$answer->appendChild($no_cron);
		}
		
		$xml->appendChild($answer);
		
		$output = $xml->saveXML();
		$this->getResponse()->setHeader('Content-Type', 'text/xml, charset=utf-8')->setBody($output);
		
	}
	
	
    public function getsetupAction() 
	{
		$xml = new DOMDocument('1.0','utf-8');
		$store = Mage::app()->getStore();
		$model = Mage::getModel('recommender/abstract');		
		$params = $this->getRequest()->getParams();
		$errorNode = $xml->createElement('strands');
		
		if (!self::DEBUG) {
			if ($params['password'] !== 'strands') {
				$errorNode->appendChild($xml->createAttribute('error'))->appendChild($xml->createTextNode('-1'));
				$xml->appendChild($errorNode);
				return;
			}
		}
		
		$setup = $xml->createElement('setup');
		$input = $xml->createElement('user_input');
		
		$internal_vars = array ( array ( Path => "recommender/account/active", Value => "", Name => "module_enabled"),
								 array ( Path => "recommender/account/strands_api_id", Value => "", Name => "api_id"),
							  	 array ( Path => "recommender/account/strands_customer_token", Value => "", Name => "customer_id"),
							  	 array ( Path => "recommender/catalog/select", Value => "", Name => "catalog_select"),
							  	 array ( Path => "recommender/cron/strands_login", Value => "", Name => "cron_login"),
							  	 array ( Path => "recommender/cron/strands_desired_catalog_time", Value => "", Name => "cron_upload_time"),
							  	 array ( Path => "recommender/cron/strands_catalog_upload_now", Value => "", Name => "cron_upload_now"),
							  	 array ( Path => "recommender/manual/strands_login", Value => "", Name => "manual_login"),
							  	 array ( Path => "design/package/name", Value => "", Name => "design_package"),
							  	 array ( Path => "design/theme/default", Value => "", Name => "design_theme"),								 
								 array ( Path => "recommender/tracking/js_tracking", Value => "", Name => "js_tracking"),
								 array ( Path => "recommender/userlogged/email_userid", Value => "", Name => "email_userid"),
								 array ( Path => "recommender/userlogged/via_js", Value => "", Name => "via_js"),
							  	 array ( Path => "recommender/widgets-homepage/active", Value => "", Name => "widgets_homepage_active"),
							  	 array ( Path => "recommender/widgets-homepage/block", Value => "", Name => "widgets_homepage_block"),
							  	 array ( Path => "recommender/widgets-homepage/position", Value => "", Name => "widgets_homepage_position"),
							  	 array ( Path => "recommender/widgets-product/active", Value => "", Name => "widgets_product_active"),
							  	 array ( Path => "recommender/widgets-product/block", Value => "", Name => "widgets_product_block"),
							  	 array ( Path => "recommender/widgets-product/position", Value => "", Name => "widgets_product_position"),							  
							   	 array ( Path => "recommender/widgets-category/active", Value => "", Name => "widgets_category_active"),
							  	 array ( Path => "recommender/widgets-category/block", Value => "", Name => "widgets_category_block"),
							  	 array ( Path => "recommender/widgets-category/position", Value => "", Name => "widgets_category_position"),							  
							  	 array ( Path => "recommender/widgets-cart/active", Value => "", Name => "widgets_cart_active"),
							  	 array ( Path => "recommender/widgets-cart/block", Value => "", Name => "widgets_cart_block"),
							  	 array ( Path => "recommender/widgets-cart/position", Value => "", Name => "widgets_cart_position"),							  
							  	 array ( Path => "recommender/widgets-checkout/active", Value => "", Name => "widgets_checkout_active"),
							  	 array ( Path => "recommender/widgets-checkout/block", Value => "", Name => "widgets_checkout_block"),
							  	 array ( Path => "recommender/widgets-checkout/positions", Value => "", Name => "widgets_checkout_position"),
							  	 array ( Path => "web/unsecure/base_url", Value => "", Name => "unsecure_base_url"),
							  	 array ( Path => "web/secure/base_url", Value => "", Name => "secure_base_url"),
							  	 array ( Path => "recommender/setup/widgets", Value => "", Name => "setup_widgets_result")
							  );
							  
		
							  
		$coreResource = Mage::getSingleton('core/resource');
		$read = $coreResource->getConnection('core_read');
		$core_config_data = $coreResource->getTableName ('core_config_data');

		for ($var_count = 0; $var_count < count($internal_vars); $var_count++) {
			$sql = "SELECT value FROM $core_config_data WHERE path='".$internal_vars[$var_count]['Path']."'";
			$lcdb = $read->fetchAll($sql);
			if (count($lcdb)) {
				$internal_vars[$var_count]['Value'] = $lcdb[0]['value'];
				$input->appendChild($xml->createElement($internal_vars[$var_count]['Name'], $internal_vars[$var_count]['Value']));
			}
		}
		
		$xml->appendChild($setup);
		$setup->appendChild($input);
		
		$pa = Mage::getModel('core/website')->load('website code')->getStores();
		
		$output = $xml->saveXML();
		$this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')->setBody($output);	
	}
	
	
    
}

?>