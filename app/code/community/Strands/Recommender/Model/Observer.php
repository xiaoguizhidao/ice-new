<?php

Class Strands_Recommender_Model_Observer extends Strands_Recommender_Model_Abstract
{
	
	
	/**
	 * replacement constructor method
	 */
	protected function _construct()
	{
		parent::_construct();
	}
	
	
	/**
	 * call method for events
	 * At call we make a url call to strands
	 * 
	 * @param $observer observer from the dispatch Event
	 */
	public function eventCartChange($observer)
	{
		if (!$this->isActive()) return;
		
		if (Mage::getStoreConfigFlag('recommender/tracking/js_tracking')) return;
		
		$api = Mage::getSingleton('recommender/api');
		
		$api->setApiAction('addshoppingcart')
				->setUser($this->getStrandsId(true))
				->setCartItems($this->getCartItems())
				->callApi();
	
		return $this;
	}	

	
	public function eventWishlistAdd($observer)
	{
		if (!$this->isActive()) return;
		$api = Mage::getSingleton('recommender/api');
		$product = $observer->getEvent()->getProduct();
		
		$api->setApiAction('addwishlist')
				->setUser($this->getStrandsId(true))
				->setProduct($product->getId())
				->callApi();
	
		return $this;
	}
	
	
	public function eventUserLogin($observer)
	{
		if (!$this->isActive()) return;
		
		if (Mage::getStoreConfigFlag('recommender/userlogged/via_js')) return;
		
		$api = Mage::getSingleton('recommender/api');
		$session = Mage::getSingleton("customer/session");
		
		if (strlen(Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_P_UserId'))) {
			Mage::getModel('core/cookie')->set('strandsSBS_P_UserId',$session->getCustomerId(),true,'/',null,null,false);
		} else {
			if (strlen(Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_S_UserId'))) {
				$api->setApiAction('userlogged')
					->setOldUser(Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_S_UserId'))
					->setUser($this->getStrandsId(true))
					->callApi();
			} else {
				Mage::getModel('core/cookie')->set('strandsSBS_P_UserId',$session->getCustomerId(),true,'/',null,null,false);
			}
		} 

		return $this;
	}
	
	
	public function eventPostDispatchSearch($observer)
	{
		if (!$this->isActive()) return;
		$api = Mage::getSingleton('recommender/api');
		$data = Mage::helper('recommender');
		
		$api->setApiAction('searched')
			->setSearchKeywords(Mage::app()->getFrontController()->getRequest()->getParam('q'))
			->setUser($this->getStrandsId(true))
			->callApi();
		
		return $this;
	}
	
	
	public function eventPostDispatchProductView($observer)
	{
		if (!$this->isActive()) return;
		
		if (Mage::getStoreConfigFlag('recommender/tracking/js_tracking')) return;
		
		$api = Mage::getSingleton('recommender/api');
		$data = Mage::helper('recommender');
		
		$api->setApiAction('visited')
			->setProduct($this->getCurrentProduct()->getId())
			->setUser($this->getStrandsId(true))
			->callApi();
			
		return $this;
	}
	
	/**
	 * Check if a setting has changed
	 * @param setting to check
	 */
	private function checkConfigChange($setting){
		return Mage::getStoreConfig('recommender/account/'.$setting) != $groups['account']['fields'][$setting]['value'];
	}
	
	/**
	 * Get package or template information.
	 *
	 * @param $termDB
	 */
	private function getPackageTheme($termDB){
		$coreResource = Mage::getSingleton('core/resource');
		$read = $coreResource->getConnection('core_read');
		$config_data = $coreResource->getTableName ('core_config_data');
	
		$termDB = '"'.$termDB.'"';
		$sql = "SELECT value FROM $config_data WHERE path=$termDB";
		$resultDB = $read->fetchAll($sql);
			
		if (count($resultDB) == 0){
			$result = 'default';
		} else{
			$result = $resultDB[0]['value'];
			if ($result == '' || $result == null ){
				$result = 'default';
			}
		}
		return $result;
	}
	
	/**
	 * Get directory name to create layout or template files. If it doesn't exist create the directory.
	 *
	 * @param $package
	 * @param $themes
	 * @param $type: "layout" or "template"
	 * @return directory name
	 */
	private function getDirectory($package, $themes, $type){
	
		$absPath = Mage::getBaseDir() . '/app/design/frontend/';
	
		if ($package=='' || !file_exists($absPath.$package) ){
			$package = 'default';
		}
		if ($themes=='' || !file_exists($absPath.$package.$themes)){
			$themes = 'default';
		}
	
		$dirFinalPT = $absPath.$relPath.$package.'/'.$themes; // .../app/design/frontend/$package/$themes
	
		if (!file_exists($dirFinalPT)) {
			// It is not possible to locate the right directory
			Mage::getConfig()->saveConfig('recommender/setup/packagetheme','Not possible to locate the right package/theme in '.$dirFinalPT);
			return;
		}
	
		$dirFinalPTtype = $dirFinalPT.'/'.$type;
	
		if (!file_exists($dirFinalPTtype)) {
			mkdir($dirFinalPTtype, 0766, true);
		}
		
		if($type=="template"){
			if (!file_exists($dirFinalPTtype.'/strands')) {
				mkdir($dirFinalPTtype."/strands", 0766, true);
			}
			if (!file_exists($dirFinalPTtype.'/strands/recommender')) {
			mkdir($dirFinalPTtype."/strands/recommender", 0766, true);
			}
		}		
		
		if (!file_exists($dirFinalPTtype)) {
			Mage::getConfig()->saveConfig('recommender/setup/packagetheme', $type . ' folder is not available in ' . $dirFinalPT);
			return;
		}
		return $dirFinalPTtype;
	}
	
	/**
	 * Create layout updates to insert the recommendation widget.
	 *
	 * @param handler to update
	 * @param observer
	 * @return xml block to update a handler
	 */
	private function getHandlerUpdate($handler, $groups){
	
		switch ($handler) {
			case 'cms_index_index':
				$group='homepage';
				break;
			case 'catalog_product_view':
				$group='product';
				break;
			case 'catalog_category_view':
				$group='category';
				break;
			case 'checkout_cart_index':
				$group='cart';
				break;
			case 'checkout_onepage_success':
				$group='checkout';
				break;
			case 'checkout_multishipping_success':
				$group='checkout';
				break;
			default:
				return '';
		}
	
		// Get the configuration from the screen
		$handlerConfig = $groups['widgets-'.$group]['fields'];
		// Get structural block
		$structural = $handlerConfig['structural']['value'];
	
		// If "automatic insertion" is not selected for this handler return empty string
		if(intval($handlerConfig['active']['value']) != 2){
			$xml='
			<'.$handler.'>				
				<reference name="before_body_end">
					<block type="recommender/js" name="recommender.js" after="-"/>
				</reference>
			</'.$handler.'>';
			return $xml;
		}
	
		// If structural block is empty 
		if ($structural == '') {
			$structural = 'before_body_end';
		}
	
		// Get position
		$position = $handlerConfig['position']['value'];
		if ($position == '1') {
			$position = 'after';
		}else{
			$position='before';
		}
	
		// Get block
		$block = $handlerConfig['block']['value'];
		if ($block == '') {
			$block = '-';
		}
	
		// Write and return the layout update for this handler
		$xml='
	<'.$handler.'>
		<reference name="'.$structural.'">
			<block type="recommender/widget" name="recommender.widget" '.$position.'="'.$block.'"/>
		</reference>
		<reference name="'.$structural.'">
			<block type="recommender/js" name="recommender.js" after="recommender.widget"/>
		</reference>
	</'.$handler.'>';
		
		/*
		$xml='
	<'.$handler.'>
		<reference name="'.$structural.'">
			<block type="recommender/widget" name="recommender.widget" as="recommender_widget" '.$position.'="'.$block.'"/>
			<block type="recommender/js" name="recommender.js" as="recommender_js" after="recommender.widget"/>
			<action method="setTemplate"><template>strands/recommender.phtml</template></action>
		</reference>
	</'.$handler.'>';
		*/
		
		return $xml;
	}
	
	/**
	 * To execute any time the Strands Recommender configuration is saved 
	 * 
	 * @param unknown $observer
	 * @return Strands_Recommender_Model_Observer
	 */
	public function eventPreDispatch($observer)
	{
		$api = Mage::getSingleton('recommender/api');
		$data = Mage::helper('recommender');
		
		$cont = $observer->getEvent()->getControllerAction();
		$groups = $cont->getRequest()->getPost('groups');
		
		if ($cont->getRequest()->getParam('section') == 'recommender') {
			$callApi = false;
			
			// Check if the plugin has been enabled or disabled
			if (intval(Mage::getStoreConfigFlag('recommender/account/active')) != intval($groups['account']['fields']['active']['value'])) {
				// Enable plugin
				if ($groups['account']['fields']['active']['value']) {
					$api->setBaseApiUrl('https://recommender.strands.com/account/plugin/setup/');
					$api->setTarget('setup');
					$callApi = true;	
				}
				// Deactivate plugin
				else {
					$api->setBaseApiUrl('https://recommender.strands.com/account/plugin/deactivate/');
					$api->setTarget('deactivate');
					$callApi = true;
				}	
			}
			
			// Check if any of the account settings has changed
			if ($this->checkConfigChange('strands_api_id') || $this->checkConfigChange('strands_customer_token') || $this->checkConfigChange('select')){
				$api->setBaseApiUrl('https://recommender.strands.com/account/plugin/setup/');
				$api->setTarget('setup');
				$callApi = true;
			}
			
			// Send api call if needed
			if ($callApi) {
				if ($groups['catalog']['fields']['select']['value'] === '0')
					$feedactive = 'true';
				else
					$feedactive = 'false';				
					
				$api->setType('magento')
					->setMessage(false)
					->setRecommenderApiId($groups['account']['fields']['strands_api_id']['value'])
					->setRecommenderCustomerToken($groups['account']['fields']['strands_customer_token']['value'])
					->setPassvApiUrl(htmlentities(Mage::app()->getStore()->getBaseUrl()."recommender/index"))
					->setFeedActive($feedactive)
					->callApi();
			}	

			// If the catalog has been uploaded via FTP save current time as last successful catalog upload time.			
			if (intval($groups['cron']['fields']['strands_catalog_upload_now']['value']) == 1) {
				Mage::getConfig()->saveConfig('recommender/cron/strands_uploadcatalognow',time());
			}
						
			// Get current package and theme 
			$package = $this->getPackageTheme('design/package/name');
			$theme = $this->getPackageTheme('design/theme/default');
			
			// Create/update layout file according to the current configuration
			$layoutFileName = $this->getDirectory($package, $theme, 'layout').'/strands_recommender.xml';
			$layoutFile = fopen($layoutFileName, 'w');
			$txt = '<?xml version="1.0"?>
						<layout version="1.0.0">'.'
								'.$this->getHandlerUpdate('cms_index_index', $groups).'
								'.$this->getHandlerUpdate('catalog_product_view', $groups).'
								'.$this->getHandlerUpdate('catalog_category_view', $groups).'
								'.$this->getHandlerUpdate('checkout_cart_index', $groups).'
								'.$this->getHandlerUpdate('checkout_onepage_success', $groups).'
								'.$this->getHandlerUpdate('checkout_multishipping_success', $groups).'
						</layout>';
			fwrite($layoutFile, $txt);
			fclose($layoutFile);
			chmod($layoutFileName,0755);
			if (!file_exists($layoutFileName)) {
				Mage::getConfig()->saveConfig('recommender/setup/layout','strands_recommender.xml file could not be created');
			}

			// Create/update template file according to the current configuration
			$templateFileName = $this->getDirectory($package, $theme, 'template').'/strands/recommender/widget.phtml';
			
			$templateFile = fopen($templateFileName, 'w');
			$txt='<div class="strandsRecs"
					tpl="<?php echo $this->getWidgetName() ?>"
					<?php echo ($this->getItem()) 		? "item=\\"{$this->getItem()}\\"" : \'\' ?>
					<?php echo ($this->getDFilter()) 	? "dfilter=\\"{$this->getDFilter()}\\"" : \'\' ?>
				  ></div>';
			fwrite($templateFile, $txt);
			fclose($templateFile);
			chmod($templateFileName,0755);
			if (!file_exists($templateFileName)) {
				Mage::getConfig()->saveConfig('recommender/setup/template','widget.phtml file could not be created');
			}		
		}		
		return $this;
	}
	
	public function eventPayInvoice($observer)
	{
		if (!$this->isActive()) return;
		
		if (Mage::getStoreConfigFlag('recommender/tracking/js_tracking')) return;
		
		$api = Mage::getSingleton('recommender/api');
		$order = $observer->getEvent()->getPayment()->getOrder();
		$items = $order->getItemsCollection();
		
		$collection = array();
		foreach ($items as $item){
			$type = $item->getProductType();
				
			switch($type){
				case 'configurable':
					if ($item->getHasChildren()) {
						$thisItem = urlencode($item->getProductId());
					}elseif($item->getParentItem() != null)	{
						$thisItem = urlencode($item->getParentItem()->getProductId());
					}
					break;
				case 'grouped':
					$values=$item->getProductOptionByCode('info_buyRequest');
					$parentId = $values['super_product_config']['product_id'];
					$thisItem = $parentId;
					break;
				case 'bundle':
					continue;
					break;
				case 'simple':
					if ($item->getParentItem() != null)	{
						$thisItem = urlencode($item->getParentItem()->getProductId());
					} else {
						$thisItem = urlencode($item->getProductId());
					}
					break;
				default:
					$thisItem = urlencode($item->getProductId());						
			}
			$collection[$thisItem]=$collection[$thisItem]+$item->getPriceInclTax()*$item->getQtyOrdered();
		}		
		
		$itemsUrl = '';
		
		foreach ($collection as $item => $value) {
			$itemsUrl .= "&item=" . urlencode($item . "::" . $value . "::1");
		}
		
		if ($order->getOrderCurrencyCode() != null)
			$currency_code = $order->getOrderCurrencyCode();
		else 
			$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
			
		$orderId = $order->getIncrementId();
		
		$api->setApiAction('purchased')
			->setUser($this->getStrandsId(true))
			->setItems($itemsUrl)
			->setOrderId($orderId)
			->setCurrency($currency_code)
			->callApi();
	}
	
	// This is the function that is called in order to perform cron-powered scheduled catalog uploads 
	public function uploadCatalog()
	{
		
		if (!$this->isActive()) return;
		
		Mage::getConfig()->saveConfig('recommender/cron/last_cron_action', Mage::getModel('core/date')->timestamp(time()));
		
		// Check if this is the scheduled time for the upload
		$timeBefore = date(DATE_RSS);
		
		$timeCatalog = $this->getDesiredCatalogTime();		
		
		if ($timeCatalog == null)
			$timeCatalog = '00:00:00';

		$currentTime = date ("H:i:s", Mage::getModel('core/date')->timestamp(time()));
		$currentHour = $currentTime[0].$currentTime[1];
		$currentMinute = $currentTime[3].$currentTime[4];	
		$catalogHour = $timeCatalog[0].$timeCatalog[1];
		$catalogMinute = $timeCatalog[3].$timeCatalog[4];

		// If this is not the schuduled time for the upload return.	
		if (($this->getUploadCatalogNow()+65 < time()) && (($currentHour != $catalogHour) || ($currentMinute != $catalogMinute))) {
			return;
		}
		// Upload the catalog	
		$ftpuser = $this->getLoginCron();
		$ftppass = $this->getPasswordCron();
		
		$ftp = $this->ftpCatalog($ftpuser,$ftppass);
		// Register the time of the last upload.
		if ($ftp) {
			Mage::getConfig()->saveConfig('recommender/catalog/strands_catalog_time',date('D, d M Y H:i:s' , Mage::getModel('core/date')->timestamp(time())));
			Mage::getConfig()->saveConfig('recommender/cron/strands_catalog_upload_now',0);
		}
	
	}
	
	// This is the function that is called in order to perform catalog uploads via FTP
	public function ftpCatalog($ftpuser,$ftppass)
	{
		$localfile = "strandsCatalog.xml";
		$absPath = Mage::getBaseDir();
		$localpath = $absPath."/media/";
		
		//$this->extractCatalog($localpath,$localfile);
		$this->writeCatalog($localpath, $localfile);
		
		$timeAfter = date(DATE_RSS);
		
		$ftpserver = "recommender.strands.com";
		$ftppath = "/catalog/complete";
		
		$remoteurl = "ftp://${ftpuser}:${ftppass}@${ftpserver}${ftppath}/${localfile}";
		
		$fp = curl_init(); 
		
		$lc = fopen("$localpath$localfile","r");
		
		curl_setopt($fp, CURLOPT_URL, $remoteurl);
//		curl_setopt($fp, CURLOPT_BINARYTRANSFER, 0);
		curl_setopt($fp, CURLOPT_UPLOAD, 1);
		curl_setopt($fp, CURLOPT_INFILE, $lc);
		
		$success = curl_exec($fp);
		curl_close($fp);
		fclose($lc);
		
		if ($success) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Method to retrive cart collection
	 * 
	 * @return Mage_Sales_Model_Qoute?
	 */
	protected function getCartItems()
	{
		 return Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection(); 
	}
	
	
	
	
	
}

?>
