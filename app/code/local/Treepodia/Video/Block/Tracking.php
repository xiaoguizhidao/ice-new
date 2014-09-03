<?php
class Treepodia_Video_Block_Tracking extends Mage_Core_Block_Template
{
	
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
		if (!$this->getTemplate() || !$this->getEnabled()) {
			return '';
		}
		$html = $this->renderView();
		return $html;
    }
		
	public function getEnabled()
	{
		return Mage::getStoreConfig('video/general/enabled');
	}
	
	public function getStoreUUID()
	{
		return Mage::getStoreConfig('video/general/store_uuid');	
	}
	
	private function getOrderedItems()
	{
		$order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
		$items = $order->getAllVisibleItems();
		$itemcount=count($items);
		$sku=array();	
		foreach ($items as $itemId => $item) :
			$sku[]=$item->getSku();
		endforeach;
		
		$orderedItems = array();
		$orderedItems['sku'] = $sku;
		
		return $orderedItems;
	}
	
	public function getTrackingHtml()
	{
		$_html = '';
		$orderedItems = $this->getOrderedItems();
		if( !empty($orderedItems) ) :
			$_html = '<script type="text/javascript">document.write(unescape("%3Cscript src=\'" + document.location.protocol + "//api.treepodia.com/video/Treepodia.js\' type=\'text/javascript\'%3E%3C/script%3E"));</script>';
			
			if( Mage::getStoreConfig('video/configuration/advance/google_ga') )
				$_html .= '<script type="text/javascript">document.write(unescape("%3Cscript src=\'" + document.location.protocol + "//dxa05szpct2ws.cloudfront.net/TreepodiaGA.js\' type=\'text/javascript\'%3E%3C/script%3E"));</script>';
			
			$_html .= '<script type="text/javascript">
				function initTreepodia () {';
			foreach( $orderedItems['sku'] as $items => $sku ) :
				$_html .= "try { Treepodia.getProduct('" . $this->getStoreUUID() . "','" . $sku . "').logAddToCart(); ";
				
				if( Mage::getStoreConfig('video/configuration/advance/google_ga') )
					$_html .= "if(isPlayedVideo()) {
								  trpdGASetEvent(\"\", '" . $sku . "', \"Checkout with video view\", 1);
								}
								else if (isCgVideo()) {
									  trpdGASetEvent(\"\", '" . $sku . "', \"Checkout video control group\", 1);
								}
								else {
								  trpdGASetEvent(\"\", '" . $sku . "', \"CheckOut without video view\", 1);
								}";

				$_html .= "} catch (e) {}";
			endforeach;
			$_html .= '}
			</script>';
		endif;
		return $_html;
	}
}
?>