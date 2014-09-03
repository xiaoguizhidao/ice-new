<?php

Class Strands_Recommender_Model_Api extends Strands_Recommender_Model_Abstract
{
	const DEBUG = false;	
	protected $_items = null;
	protected $_param = array();
		
	public function _construct()
	{
		$this->setBaseApiUrl("http://bizsolutions.strands.com/api2/event/");
	}
		
	/**
	 * Method for Api Call 
	 * 
	 * @param $args array of different args for the call
	 */
	public function callApi()
	{
		$session = Mage::getSingleton('core/session');
		
		//if (!$this->isActive()) return;	
		///$apiCall = $this->getBaseApiUrl() . $this->getApiAction() . '.sbs?apid=' . $this->getApiId();
		
		$apiCall = $this->getBaseApiUrl();
		
		if ($this->getApiAction()) {
			$apiCall = $apiCall . $this->getApiAction() . '.sbs?apid=' . $this->getApiId();
		} else {
			$apiCall = $apiCall . '?apid=' . $this->getApiId() . '&token=' . $this->getCustomerToken() . '&version=' . $this->getVersion();
			//$apiCall = $apiCall . '&version=' . $this->getVersion();
			if ($this->getTarget() == 'setup') {// Plugin activation.
				$apiCall = $apiCall . '&id=id&title=title&link=link&description=description&image_link=image_link&price=saleprice&tag=tag&category=category';
			}
		}		

		if ($this->getApiAction() == 'userlogged'){
			/*
			 * Block for user logged in action
			 */
			$apiCall .= "&olduser=" . $this->getOldUser();			
		} 
		elseif ($this->getApiAction() == "addshoppingcart") {
			/*
			 * Block for modifying the shopping cart
			 */			
			if (count($this->getCartItems())) {
				$_items = '';							
				foreach ($this->getCartItems() as $item) {
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
							$values=unserialize($item->getOptionByCode('info_buyRequest')->getValue());
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
					$_items .= "&item=" . $thisItem;
				}
				$apiCall .= $_items;
			}
		}

		
		if ($this->hasData('product')) $apiCall .= "&item=" . urlencode($this->getProduct());
		if ($this->hasData('items')) $apiCall .= $this->getItems();
		if ($this->hasData('order_id')) $apiCall .= "&orderid=" . $this->getOrderId();
		if ($this->hasData('search_keywords')) $apiCall .= "&searchstring=" . urlencode($this->getSearchKeywords());
		if ($this->hasData('user') && strlen($this->getUser())) $apiCall .= "&user=" . $this->getUser();
		if ($this->hasData('type')) $apiCall .= "&type=" . $this->getType();
		if ($this->hasData('passv_api_url')) $apiCall .= "&feed=" . $this->getPassvApiUrl();
		if ($this->hasData('feed_active')) $apiCall .= "&feedactive=" . $this->getFeedActive();
		if ($this->hasData('currency')) $apiCall .= "&currency=" . $this->getCurrency();
		
		$response = $this->makeCall($apiCall);
				
		if ($this->getLogStatus()) {
			$message = ($response['success']) ? 'Success' : 'Failed';
			$message .= ": Request Call: $apiCall\r\n";
			$message .= $response['message'];
					
			if (!$success = Mage::helper('recommender/logger')->log($message)) {
				//$session->addError('Recommender log failed to instantate or write');
			}			 
		}
		
		//if ($response['success']) $session->addError($response['message']);
		//else $session->addError($response['message']);
		
		return $this;
	}
	
	
	/**
	 * Method for cURL call
	 * 
	 * @param $url is the URL we are calling
	 * @return array response
	 */
	protected function makeCall($url, $params = array())
	{
		$ret = array('success' => true);
		
		try {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    		curl_setopt($curl, CURLOPT_HEADER, false);
    		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //
    		//curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
    		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($curl, CURLOPT_USERAGENT, "Magento Strands Recommender v1.0");
    		
			if (isset($params['CURLOPT']) && count($params['CURLOPT'])) {
    			foreach ($params['CURLOPT'] as $k => $v) {
    				curl_setopt($curl, constant("CURLOPT_$k"), $v);
    			} 
    		}
    	
    		$ret['message'] = curl_exec($curl);
    		$ret['message'] = ($this->getMessage()) ? $ret['message'] : 'Success';
    		
    		curl_close($curl);
		} catch(Exception $e) {
			$ret['success'] = false;
			$ret['message'] = $e->getMessage();
		}
		
    	return $ret;
	}
	
	
	public function setParam($key, $val) 
	{
		$this->_param[$key] = $val;
		return $this;
	}
	
	protected function getMessage()
	{
		if (!$this->hasData('message')) $this->setMessage(true);
		return $this->getData('message');
	}
	
	public function checkCatalogFeed()
	{
		$apiCall = 'http://recommender.strands.com/account/plugin/feedtest/';
		$apiCall.= '?apid='.$this->getApiId();
		$apiCall.= '&token='.$this->getCustomerToken();
		$apiCall.= '&type=magento';
		$apiCall.= '&url=';
		$apiCall.= htmlentities(Mage::getUrl("recommender/index", array('_secure' => true)));
		$apiCall.= 'feedtest?';
		$apiCall.= 'api_id='.$this->getApiId();
		$apiCall.= '&customer_id='.$this->getCustomerToken();

		$response = $this->makeCall($apiCall);
		if ($response['success']) {
			$xml = simplexml_load_string($response['message']);
			$result = (string) $xml->status;
			if ($result == 'SUCCESS')
				return true;
			else 
				return false;
		} 
		return false;
	}	
}

?>