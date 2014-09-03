<?php

Class Strands_Recommender_Block_Widget extends Mage_Core_Block_Template
{
	protected $_checkout = null;
	protected $_quote = null;
	protected $_template = "strands/recommender/widget.phtml";
	protected $helper = null;
		
		
	public function _construct() 
	{
		parent::_construct();
		$this->helper = Mage::helper('recommender');
	}

	
	/**
	 * Function is set to blank to prevent the Block from Auto Rendering
	 */
	protected function _toHtml() 
	{
		$model = Mage::getModel('recommender/abstract');
		if (!$model->isActive()) {
            return '';
        }
        
        if (!$this->hasData('widget_name')) $this->setWidgetName($this->_getTemplate());
		//if (!$this->hasData('widget_name')) $this->setWidgetName($this->helper->getCurrentPage());
		if (!$this->hasData('item')) $this->setItem($this->getBasketItems());
		/* Commented by me. Not needed anymore
		if (!$this->hasData('strands_id')) $this->setStrandsId($model->getStrandsId());
		*/
		return parent::_toHtml();	
	} 
	
	
	/**
	 * @return the Items in the Basket or Items Ordered
	 */
	protected function getBasketItems()
	{
		
		if ($_product = Mage::registry('product')) {
			return urlencode($_product->getId());
		}
		elseif ($this->helper->getCurrentPage() == "checkout_cart_index" || $this->helper->getCurrentPage() == "checkout_cart") {
			$ret = array();
			
			
			foreach ($this->getQuote()->getAllVisibleItems() as $item) {
				$ret[] = urlencode($item->getProductId());
			}

			return implode("_._", $ret);
		} 
		elseif ($this->helper->getCurrentPage() == "checkout_onepage_success" 
					|| $this->helper->getCurrentPage() == "checkout_success" 
					|| $this->helper->getCurrentPage() == "checkout_success_index") {

			$orders = $this->getOrders();
			$ret = array();
			
			foreach ($orders as $order) {
     			$ret[] = urlencode($this->setCurrentOrder($order)->addOrderItems()); // CHECK   		        		
			}	

			return implode("_._", $ret);
		} 
		
		return '';
	}
	
	
	/**
	 * Method to add Items to the order
	 * 
	 * @return string of array joined with _._ 
	 */
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
        
        
        $ret = array();
		foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            
            $ret[] = $item->getProductId();
		}
		
		return implode("_._",$ret);
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


	/**
	 * Function to get the default template code
	 *
	 * @return string of Template name
	 */
	protected function _getTemplate() 
	{
		switch($this->helper->getCurrentPage()) {
			case "cms_index_index":
				return "cms_index_index";
				break;

			case "catalog_product_view":
				return "catalog_product_view";
				break;
				
			case "catalog_category_view":
				return "catalog_category_view";
				break;
				
			case "checkout_cart_index":
				return "checkout_cart_index";
				break;
				
			case "checkout_onepage_success":
				return "checkout_onepage_success";
				break;
				
			case "checkout_multishipping_success":
				return "checkout_multishipping_success";
				break;
				
			default:
				return "cms_index_index";
				break;
		}
	}
	
	public function getDFilter()
	{
		if (!$this->hasData('d_filter')) {
			switch ($this->helper->getCurrentPage()) {
				case 'catalog_product_view':
					if ($this->getCatProduct()) {
						$categories = $this->getProduct()->getCategoryIds(); 
			
						$html = '';
						$i = 1;
						if (count($categories)) {
							foreach ($categories  as $_cat) {
								$html .= ($i < count($categories)) ? $_cat . "_._" : $_cat; 
							}	
							$i++;
						} else $html = false;
					} else $html = false;
					break;
				
				case 'catalog_category_view':
					$_category = Mage::registry('current_category');
					$html = 'cat_id::'.$_category->getId();
					break;
					
				default:
					$html = false;
					break;
			}
			$this->setData('d_filter', $html);
			
					
			
		}
		return $this->getData('d_filter');
	}
 
}


?>
