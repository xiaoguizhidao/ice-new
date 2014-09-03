<?php

Class Strands_Recommender_Block_Js extends Mage_Core_Block_Text
{
	protected $_checkout = null;
	protected $_quote = null;
	protected $helper = null;
	
	public function _construct() 
	{
		parent::_construct();
		$this->helper = Mage::helper('recommender');
	}
		
	protected function _toHtml() 
	{
		$model = Mage::getModel('recommender/abstract');
		
		if (!$model->isActive()) {
            return '';
        }
        
		if (Mage::getStoreConfigFlag('recommender/tracking/js_tracking')) {
        //if (true) {
        	$this->doTracking($model);
        }
        
        $protocol = Mage::app()->getStore()->isCurrentlySecure() ? 'https' : 'http';
		$this->addText('
		<script type="text/javascript" src="'.$protocol.'://bizsolutions.strands.com/sbsstatic/js/sbsLib-1.0.min.js"></script>');
		
		$this->addText('
		<script language="Javascript" type="text/javascript">
       	try {
       		SBS.Worker.go("'.$model->getApiId().'");
       	} catch(ex) { }
       	</script>');
		
		return parent::_toHtml();	
	}
	
	protected function doTracking($model) 
	{
		$page = $this->helper->getCurrentPage();
		$event = '';
		switch ($page) {
			case "catalog_product_view":
				$event = 'visited';
				break;
				
			case "checkout_cart_index":
				$event = 'addshoppingcart';
				break;
				
			case "checkout_onepage_success":
				$event = 'purchased';
				break;

			case "checkout_multishipping_success":
				$event = 'purchased';
				break;			
		}
		
		/*
		 * Only allow visited event
		 */		
		// UNCOMMENT THIS TO ROLL BACK TO ORIGINAL: if ($event !== 'visited') return;

		if ($event !== '') {
		
			$this->addText('
			<script type="text/javascript">
        	if (typeof StrandsTrack=="undefined"){StrandsTrack=[];}
        	StrandsTrack.push({');
        
        	switch ($event) {
        		case "visited":
        			$this->addText('
        		event: "visited",');
        			$this->addText('
        		item: "'.$this->getItemsArray($page).'"');
        			break;
        		case "addshoppingcart":
        			$this->addText('
        		event: "addshoppingcart",');
        			$this->addText('
        		items: [
        			'.$this->getItemsArray($page).'
        		]');
        			break;
        		case "purchased":
        			$this->addText('
        		event: "purchased",');
        			$this->addText($this->getItemsArray($page));
        			break;
        		case "searched":
        			break;
        		case "addwishlist":
        			break;
        	}

			/* This is not necessary. The user parameter gets inserted properly without this.        	
        	if (!$this->hasData('strands_id')) $this->setStrandsId($model->getStrandsId());
        	if ($user = $this->getStrandsId()) {
        		$this->addText(',
        		user: "'.$user.'"');
        	}
			*/
        	/* userlogged event: */
        	if (!$this->hasData('strands_id')){
        		$this->setStrandsId($model->getStrandsId());
        	}        		 
        	if ($user = $this->getStrandsId()) {
        		// "userlogged" event is to be sent via JS
        		if (Mage::getStoreConfigFlag('recommender/userlogged/via_js')){        			
        			$this->addText('
        	});');
        			$this->addText('
        		StrandsTrack.push({');
        			$this->addText('
        			event: "userlogged",');
        			$this->addText('
   					user: "'.$user.'"');
        		}       		        	      	
        	}
    	   	$this->addText('
			});
 		   	</script>');
		}
	}

	/**
	 * @return the Items in the Basket or Items Ordered
	 */
	protected function getItemsArray($currentPage)
	{
		
		if ($_product = Mage::registry('product')) {
			return $_product->getId();
		}
		elseif ($currentPage == "checkout_cart_index" 
					|| $currentPage == "checkout_cart") {
			$ret = array();
			
			
			foreach ($this->getQuote()->getAllVisibleItems() as $item) {
				//$ret[] = '"'.$item->getProductId().'"';
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
				
				$ret[] = '"'.$thisItem.'"';
				
			}

			return implode(",", $ret);
		} 
		elseif ($currentPage == "checkout_onepage_success" 
					|| $currentPage == "checkout_success" 
					|| $currentPage == "checkout_success_index") {

			$orders = $this->getOrders();
			$ret = array();
			
			foreach ($orders as $order) {
     			$ret = $this->setCurrentOrder($order)->addOrderItems(); 		        		
			}	

			return $ret;
		} 
		
		return '';
	}
	
	/**
	 * Method borrowed from Mage_Catalog_Block_Product
	 * 
	 * @return product or false
	 */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
	
	/**
     * Get active quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
    
	/**
	 * From Mage_Checkout_Block_Cart_Abstract
     * Get checkout session
     *
     * @return Mage_Checkout_Model_session
     */
    public function getCheckout()
    {
        if (null === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    protected function getOrders()
    {
    	$quote = $this->getCheckout()->getLastQuoteId();
        if (!$quote) {
            return '';
        }

        if ($quote instanceof Mage_Sales_Model_Quote) {
            $quoteId = $quote->getId();
        } else {
            $quoteId = $quote;
        }

        if (!$quoteId) {
            return '';
        }

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToFilter('quote_id', $quoteId)
            ->load();
           
        return $orders;    
    }
    
	protected function addOrderItems() 
	{
		$order = $this->getCurrentOrder();
        if (!$order) {
            return '';
        }

        if (!$order instanceof Mage_Sales_Model_Order) {
            $order = Mage::getModel('sales/order')->load($order);
        }

        if (!$order) {
            return '';
        }	
        
        if ($order->getOrderCurrencyCode() != null)
			$currency_code = $order->getOrderCurrencyCode();
		else 
			$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
			
		$orderId = $order->getIncrementId();
        
        $ret = array();
		foreach ($order->getAllItems() as $item) {            
            
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
			$ret[] = '{id:"'.$thisItem.'",price:"'.$item->getPriceInclTax().'",quantity:"'.intval($item->getQtyOrdered()).'"}';
			
			/*
            if ($item->getHasChildren()) {
				continue;
			} else {
				if ($item->getParentItem() != null)	{
					$ret[] = '{id:"'.$item->getParentItem()->getProductId().'",price:"'.$item->getParentItem()->getPriceInclTax().'",quantity:"'.intval($item->getParentItem()->getQtyOrdered()).'"}';
				} else {
					$ret[] = '{id:"'.$item->getProductId().'",price:"'.$item->getPriceInclTax().'",quantity:"'.intval($item->getQtyOrdered()).'"}';
				}
			}
			*/
		}
		
		$itemsArray = implode(",",$ret);
		
		$itemsArray = '
            items:[
            	'.$itemsArray.'
            ],
            orderid: "'.$orderId.'"';
		
		return $itemsArray;
	}
	
}


?>