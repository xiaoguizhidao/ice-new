<?php

Class Strands_Recommender_Model_Abstract extends Mage_Core_Model_Abstract
{

	public function getVersion()
	{
		$version = Mage::getConfig()->getNode('modules')->Strands_Recommender->version;
		return $version;
		
	}
	
	
	/**
	 * Method to retrieve Strands Id Stored in Cookie
	 * 
	 * @return self with data
	 */
	public function getStrandsId($logged = false) 
	{
		if (!$this->hasData('strands_id')) {
			$session = Mage::getSingleton("customer/session");
			
			if ($session->isLoggedIn()) {
				if ( Mage::getStoreConfigFlag('recommender/userlogged/email_userid') && ($email = $session->getCustomer()->getEmail()) != '') {
					$this->setStrandsId($email);
				}else{
					$this->setStrandsId($session->getCustomerId());
				}
				if( Mage::getStoreConfigFlag('recommender/tracking/js_tracking') && !Mage::getStoreConfigFlag('recommender/userlogged/via_js')){
					Mage::getModel('core/cookie')->set('strandsSBS_P_UserId', $this->getData('strands_id'), true, '/', null, null, false);
				}			
			} elseif ($logged) {
				if (strlen(Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_P_UserId')))
					return Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_P_UserId');
				elseif (strlen(Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_S_UserId')))
					return Mage::app()->getFrontController()->getRequest()->getCookie('strandsSBS_S_UserId');
				else {
					$newCookie = $this->create_anon_cookie();
					Mage::getModel('core/cookie')->set('strandsSBS_S_UserId',$newCookie,true,'/',null,null,false);
					return $newCookie;
				}
			} else {
				$this->setStrandsId('');
			}
		}
		return $this->getData('strands_id');
		
	}
	
	/**
	 * Method to create an anonymous cookie
	 */
	private function create_anon_cookie()
	{	
		
		//$cookie = 'MGN_'.rand(0,1000).(((int)(time()/1000))*rand(0,1000)).rand(0,1000).'_'.$this->ms_time();
		$cookie = 'MGN_'.$this->ms_time().'_'.rand(0,100000);
		return $cookie;
	}
	
	private function ms_time()
	{
		$timeday = gettimeofday();
		$sec = intval($timeday['sec']);
		$msec = intval(floor($timeday['usec']/1000));
		if (strlen($msec) == 2) $msec = '0'.$msec;
		elseif (strlen($msec) == 1) $msec = '00'.$msec;
			
		return $sec.$msec;
	}
	
	/**
	 * Method to check if the module is active
	 * 
	 * @return bool
	 */
	public function isActive()
	{
		if (!Mage::getStoreConfigFlag('recommender/account/active')) return false;
		if (strlen($this->getApiId()) < 1) return false;
		if (strlen($this->getCustomerToken()) < 1) return false;
		return true;
	}
	
	/**
	 * Method to check if the catalog must be uploaded in that very moment
	 * 
	 * @return bool
	 */
	public function getUploadCatalogNow()
	{
		if (!$this->hasData('recommender_uploadcatalognow')) {
			$this->setData('recommender_uploadcatalognow', Mage::getStoreConfig('recommender/cron/strands_uploadcatalognow'));
		}
		return $this->getData('recommender_uploadcatalognow');
	}
	
	/**
	 * Method to retrieve api id
	 * 
	 * @return self 
	 */
	public function getApiId() 
	{
		if (!$this->hasData('recommender_api_id')) {
			$this->setData('recommender_api_id', Mage::getStoreConfig('recommender/account/strands_api_id'));
		}
		return $this->getData('recommender_api_id');	
	}
	
	
	/**
	 * Method to retrieve customer token
	 * 
	 * @return self 
	 */
	public function getCustomerToken() 
	{
		if (!$this->hasData('recommender_customer_token')) {
			$this->setData('recommender_customer_token', Mage::getStoreConfig('recommender/account/strands_customer_token'));
		}
		return $this->getData('recommender_customer_token');	
	}
	
	
	/**
	 * Method to retrieve strands login
	 * 
	 * @return self
	 */
	public function getLoginCron()
	{
		if (!$this->hasData('recommender_login')) {
			$this->setData('recommender_login', Mage::getStoreConfig('recommender/cron/strands_login'));
		}
		return $this->getData('recommender_login');
	}
	
	
	/**
	 * Method to retrieve strands password
	 * 
	 * @return self
	 */
	public function getPasswordCron()
	{
		if (!$this->hasData('recommender_pass')) {
			$this->setData('recommender_pass', Mage::getStoreConfig('recommender/cron/strands_password'));
		}
		return $this->getData('recommender_pass');
	}
	
	
	/**
	 * Method to retrieve desired catalog upload time 
	 */
	public function getDesiredCatalogTime()
	{
		if (!$this->hasData('recommender_desired_catalog_time')) {
			$this->setData('recommender_desired_catalog_time', Mage::getStoreConfig('recommender/cron/strands_desired_catalog_time'));
		}
		return $this->getData('recommender_desired_catalog_time');
	}
	
	/**
	 * Method to retrieve Log status
	 * 
	 * @return self with data
	 */
	public function getLogStatus()
	{
		if (!$this->hasData('log_status')) {
			$this->setData('log_status', Mage::getStoreConfig('recommender/account/log'));
		}
		return $this->getData('log_status');
	}
	
	
	public function getLastCronAction()
	{
		if (!$this->hasData('last_cron_action')) {
			$this->setData('last_cron_action', Mage::getStoreConfig('recommender/cron/last_cron_action'));
		}
		return $this->getData('last_cron_action');
	}
	
	
	public function getLoginManual()
	{
		if (!$this->hasData('recommender_login')) {
			$this->setData('recommender_login', Mage::getStoreConfig('recommender/manual/strands_login'));
		}
		return $this->getData('recommender_login');
	}
	
	
	public function getPasswordManual()
	{
		if (!$this->hasData('recommender_pass')) {
			$this->setData('recommender_pass', Mage::getStoreConfig('recommender/manual/strands_password'));
		}
		return $this->getData('recommender_pass');
	}
	
	
	/**
	 * Method borrowed from Mage_Catalog_Block_Product
	 * 
	 * @return product or false
	 */
    public function getCurrentProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
    
    
	public function extractCatalog($localpath,$localfile,$page = 0, $maxcount = 0) 
	{
		
		$xml = new DOMDocument('1.0','utf-8');
		$store = Mage::app()->getStore();
		
		$model = Mage::getModel('recommender/abstract');
		
		$inventoryEnabled = false;
		if (Mage::getStoreConfigFlag('recommender/catalog-extra-fields/manage_stock'))
			$inventoryEnabled = true;
		
		$root = $xml->createElement('sbs-catalog');
		$root->appendChild($xml->createAttribute('version'))->appendChild($xml->createTextNode('1.0'));
		$xml->appendChild($root);
		
		$root->appendChild($xml->createElement('company'))->appendChild($xml->createTextNode(htmlentities(Mage::getStoreConfig('general/store_information/name'))));
		$root->appendChild($xml->createElement('link'))->appendChild($xml->createTextNode(htmlentities($store->getBaseUrl())));
		$root->appendChild($xml->createElement('description'))->appendChild($xml->createTextNode(htmlentities(Mage::getStoreConfig('general/store_information/name'))));
		
        $collection = Mage::getModel('catalog/product')
			->getCollection()
        	    ->addAttributeToSelect('*')
        	    ->addAttributeToFilter('status','1')
        	    ->addAttributeToFilter('visibility', array('neq' => 1))
        	    ->setPageSize($maxcount)
        	    ->setCurPage($page)
        	    ->load();
        
        if ($inventoryEnabled) {
        	$inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
        	$configManageStock = (int) Mage::getStoreConfigFlag(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        }

        $totalItems = $collection->getSize();
        $root->appendChild($xml->createElement('product_count'))->appendChild($xml->createTextNode((string)$totalItems));	    
        	    
        $collectionCats = Mage::getModel('catalog/category')
        	->getCollection()
        		->addAttributeToSelect('name')
        		->addAttributeToSelect('entity_id');	
      
        $catArray = Mage::getModel('catalog/category')
        	->getTreeModel()
        	->load()
        	->getNodes();
        				
        foreach ($collectionCats as $cCat) {
        	if (($name = $cCat->getName()) === NULL)
        		$name = '';        	
        	if (isset($catArray[$cCat->getEntityId()]))
        		$catArray[$cCat->getEntityId()]->setName($name);
        	
        }				

        if ($page <= ((int) ceil($totalItems/$maxcount))) {
        	    
			foreach ($collection as $_product) {
				
				$item = $xml->createElement('item');
				$item->appendChild($xml->createElement('id'))->appendChild($xml->createCDATASection($_product->getId()));
				$item->appendChild($xml->createElement('title'))->appendChild($xml->createCDATASection($_product->getName()));
				$item->appendChild($xml->createElement('link'))->appendChild($xml->createTextNode(htmlentities($_product->getProductUrl())));
				$item->appendChild($xml->createElement('description'))->appendChild($xml->createCDATASection($_product->getDescription()));
				if ($_product->getData('image') != 'no_selection') {
					$item->appendChild($xml->createElement('image_link'))->appendChild($xml->createTextNode(htmlentities(Mage::helper('catalog/image')->init($_product,'image'))));
					$item->appendChild($xml->createElement('small_image_link'))->appendChild($xml->createTextNode(htmlentities(Mage::helper('catalog/image')->init($_product,'small_image')->resize(135))));
				} else {
					$item->appendChild($xml->createElement('image_link'))->appendChild($xml->createTextNode(''));
					$item->appendChild($xml->createElement('small_image_link'))->appendChild($xml->createTextNode(''));
				}
				$item->appendChild($xml->createElement('saleprice'))->appendChild($xml->createCDATASection($_product->getPrice()));
				
				$item->appendChild($xml->createElement('sku'))->appendChild($xml->createCDATASection($_product->getSku()));
				
				if (($taxprice = Mage::helper('tax')->getPrice($_product,$_product->getFinalPrice(), true)) !== null) {
					$item->appendChild($xml->createElement('taxprice'))->appendChild($xml->createCDATASection($taxprice));
				} else {
					$item->appendChild($xml->createElement('taxprice'))->appendChild($xml->createTextNode(''));
				}
				
				if (($specialprice = Mage::helper('tax')->getPrice($_product,$_product->getSpecialPrice(), true)) !== null) {
					$item->appendChild($xml->createElement('specialprice'))->appendChild($xml->createCDATASection($specialprice));
				} else {
					$item->appendChild($xml->createElement('specialprice'))->appendChild($xml->createTextNode(''));
				}
				
				if ($_product->hasData('is_salable')) {
					$item->appendChild($xml->createElement('is_salable'))->appendChild($xml->createCDATASection($_product->getIsSalable()));					
				}
				if ($_product->hasData('stock_item')) {
					$_stockItem = $_product->getStockItem();
					if ($_stockItem->hasData('is_in_stock')) {
						$item->appendChild($xml->createElement('is_in_stock'))->appendChild($xml->createCDATASection($_stockItem->getIsInStock()));						
					}
				}
				if ($inventoryEnabled) {
					$_productInventory = $inventory->loadByProduct($_product);
					if (((int)$_productInventory->getUseConfigManageStock()) == 1) {
						$_manageStock = $configManageStock;
					} else {
						$_manageStock = $_productInventory->getManageStock();
					}
					$item->appendChild($xml->createElement('manage_stock'))->appendChild($xml->createCDATASection($_manageStock));	
					if ($_manageStock == 1) {
						$_stockQty = (int) $_productInventory->getQty();
						$item->appendChild($xml->createElement('stock_qty'))->appendChild($xml->createCDATASection($_stockQty));						
					}					
				}
	
				if (count($_product->getCategoryIds())) {
					foreach ($_product->getCategoryIds() as $_lastCat) {
						if (($category = $catArray[$_lastCat]) !== NULL) {
							$categLevel = $xml->createElement('category');
							foreach (explode('/',$category->getPathId()) as $_cat) {
								if ($catArray[$_cat]->getName() !== '') {
									$item->appendChild($xml->createElement('cat_id'))->appendChild($xml->createTextNode($_cat));
									$categLevel->appendChild($xml->createElement('level'))->appendChild($xml->createTextNode($catArray[$_cat]->getName()));
								}							
							}
							$item->appendChild($categLevel);
						}
					}
				}
	
				$_attributes = $this->getAdditionalData($_product);
				
				foreach ($_attributes as $_attr) {
					$item->appendChild($xml->createElement('a_'.$_attr['code']))->appendChild($xml->createCDATASection($_attr['value']));
					if ($_attr['code'] == 'small_image') {
						$item->appendChild($xml->createElement($_attr['code'].'_concat_url'))->appendChild($xml->createTextNode(htmlentities($store->getBaseUrl().'media/catalog/product'.$_attr['value'])));
					}
				}
				
	
/*			
				$_options = $_product->getOptions();
				if (count($_options)) {
					foreach($_options as $_option) {
						foreach ($_option->getData() as $_key => $_val) {
							$item->appendChild($xml->createElement('o_'.$_key))->appendChild($xml->createCDATASection($_val));
						}
						
						foreach($_option->getValues() as $_value) {
							foreach ($_value->getData() as $_key => $_val) {						
								$item->appendChild($xml->createElement('o_'.$_key))->appendChild($xml->createCDATASection($_val));
							}
						}
					}
				}
*/
						
					
				$root->appendChild($item);
			}
		
        }
	
		$output = $xml->saveXML();
		
		$sc = fopen("$localpath$localfile",'w');
		fwrite($sc,$output);
		fclose($sc);
		
	}
	
	
	public function writeCatalog($localpath,$localfile,$page = 0, $maxcount = 0) 
	{
		
		$store = Mage::app()->getStore();
		
		$model = Mage::getModel('recommender/abstract');
		
		if ($localfile == 'memory') {
			$scw = '';
			$this->memory = true;
		} else {
			$scw = fopen("$localpath$localfile",'w');
			$this->memory = false;
		}
			
		$this->catalog = '';
		
		$inventoryEnabled = false;
		if (Mage::getStoreConfigFlag('recommender/catalog-extra-fields/manage_stock'))
			$inventoryEnabled = true; 
		
		$this->writeIntoFile($scw, '?xml version="1.0" encoding="utf-8"?');

		$this->writeIntoFile($scw,'sbs-catalog version = "1.0"');
		
		$this->writeIntoFile($scw,'company',htmlentities(Mage::getStoreConfig('general/store_information/name')),1);
		$this->writeIntoFile($scw,'link',htmlentities($store->getBaseUrl()),1);
		$this->writeIntoFile($scw,'description',htmlentities(Mage::getStoreConfig('general/store_information/name')),1);
		
        $collection = Mage::getModel('catalog/product')
			->getCollection()
        	    ->addAttributeToSelect('*')
        	    ->addAttributeToFilter('status','1')
        	    ->addAttributeToFilter('visibility', array('neq' => 1))
        	    ->setPageSize($maxcount)
        	    ->setCurPage($page)
        	    ->load();

        $improvedSize = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status','1')
            ->addAttributeToFilter('visibility', array('neq' => 1))
                ->getSize();
        
        if ($inventoryEnabled) {        	
        	$inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
        	$configManageStock = (int) Mage::getStoreConfigFlag(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        }

        $totalItems = $collection->getSize();
        $this->writeIntoFile($scw,'product_count_old',(string)$totalItems,1);
        $this->writeIntoFile($scw,'product_count', (string)$improvedSize, 1);
        	    
        $collectionCats = Mage::getModel('catalog/category')
        	->getCollection()
        		->addAttributeToSelect('name')
        		->addAttributeToSelect('entity_id');	
      
        $catArray = Mage::getModel('catalog/category')
        	->getTreeModel()
        	->load()
        	->getNodes();
        				
        foreach ($collectionCats as $cCat) {
        	if (($name = $cCat->getName()) === NULL)
        		$name = '';
        	if (isset($catArray[$cCat->getEntityId()]))
        		$catArray[$cCat->getEntityId()]->setName($name);
        	
        }				

        if (($maxcount == 0) || ($page <= ((int) ceil($totalItems/$maxcount)))) {
        	    
			foreach ($collection as $_product) {
				
				$this->writeIntoFile($scw,'item');
				$this->writeIntoFile($scw,'id','<![CDATA['.$_product->getId().']]>',1);
				$this->writeIntoFile($scw,'title','<![CDATA['.$_product->getName().']]>',1);
				$this->writeIntoFile($scw,'link',htmlentities($_product->getProductUrl()),1);
				$this->writeIntoFile($scw,'description','<![CDATA['.$_product->getDescription().']]>',1);
				if ($_product->getData('image') != 'no_selection') {
					$this->writeIntoFile($scw,'image_link',htmlentities(Mage::helper('catalog/image')->init($_product,'image')),1);
					$this->writeIntoFile($scw,'small_image_link',htmlentities(Mage::helper('catalog/image')->init($_product,'small_image')->resize(135)),1);
				} else {
					$this->writeIntoFile($scw,'image_link','',1);
					$this->writeIntoFile($scw,'small_image_link','',1);
				}
				$this->writeIntoFile($scw,'saleprice','<![CDATA['.$_product->getPrice().']]>',1);
				
				$this->writeIntoFile($scw,'sku','<![CDATA['.$_product->getSku().']]>',1);
				
				if (($taxprice = Mage::helper('tax')->getPrice($_product,$_product->getFinalPrice(), true)) !== null) {
					$this->writeIntoFile($scw,'taxprice','<![CDATA['.$taxprice.']]>',1);
				} else {
					$this->writeIntoFile($scw,'taxprice','',1);
				}
				
				if (($specialprice = Mage::helper('tax')->getPrice($_product,$_product->getSpecialPrice(), true)) !== null) {
					$this->writeIntoFile($scw,'specialprice','<![CDATA['.$specialprice.']]>',1);
				} else {
					$this->writeIntoFile($scw,'specialprice','',1);
				}
				
				if ($_product->hasData('is_salable')) {
					$this->writeIntoFile($scw, 'is_salable', '<![CDATA['.$_product->getIsSalable().']]>', 1);
				}
				if ($_product->hasData('stock_item')) {
					$_stockItem = $_product->getStockItem();
					if ($_stockItem->hasData('is_in_stock')) {
						$this->writeIntoFile($scw, 'is_in_stock', '<![CDATA['.$_stockItem->getIsInStock().']]>', 1);
					}
				}
				if ($inventoryEnabled) {
					$_productInventory = $inventory->loadByProduct($_product);
					if (((int)$_productInventory->getUseConfigManageStock()) == 1) {
						$_manageStock = $configManageStock;
					} else {
						$_manageStock = $_productInventory->getManageStock();
					}
					$this->writeIntoFile($scw, 'manage_stock', '<![CDATA['.$_manageStock.']]>', 1);
					if ($_manageStock == 1) {
						$_stockQty = (int) $_productInventory->getQty();
						$this->writeIntoFile($scw, 'stock_qty', '<![CDATA['.$_stockQty.']]>', 1);
					}
				}
	
				if (count($_product->getCategoryIds())) {
					foreach ($_product->getCategoryIds() as $_lastCat) {
						if (($category = $catArray[$_lastCat]) !== NULL) {
							$this->writeIntoFile($scw,'category');
							foreach (explode('/',$category->getPathId()) as $_cat) {
								if (!is_null($catArray[$_cat])&&$catArray[$_cat]->getName() !== '')  {
									$this->writeIntoFile($scw,'level','<![CDATA['.$catArray[$_cat]->getName().']]>',1);
								}else{
									$this->writeIntoFile($scw,'level','<![CDATA[]]>',1);
								}							
							}
							$this->writeIntoFile($scw,'/category');
							foreach (explode('/',$category->getPathId()) as $_cat) {
								if ($catArray[$_cat]->getName() !== '') {
									$this->writeIntoFile($scw,'cat_id',$_cat,1);
								}							
							}
						}
					}
				}
	
				$_attributes = $this->getAdditionalData($_product);
				
				foreach ($_attributes as $_attr) {
					$this->writeIntoFile($scw,'a_'.$_attr['code'],'<![CDATA['.$_attr['value'].']]>',1);
					if ($_attr['code'] == 'small_image') {
						$this->writeIntoFile($scw,$_attr['code'].'_concat_url',htmlentities($store->getBaseUrl().'media/catalog/product'.$_attr['value']),1);
					}
				}		
					
				$this->writeIntoFile($scw,'/item');
			}
		
        }
	
		$this->writeIntoFile($scw,'/sbs-catalog');

		if ($this->memory) {
			return $this->catalog;
		} else {
			fclose($scw);
		}
		
	}
	
	
	public function writeIntoFile ($file, $tag, $value = false, $ending = false) {
		if ($value){
			$value2write = $value;
		} else {
			$value2write = '';
		}
		
		$string2write = "<".$tag.">".$value2write;
		
		if ($ending) {
			$string2write .= "</".$tag.">";
		}
		
		$string2write .= "\n";
		
		if ($this->memory) {
			$this->catalog .= $string2write;
		} else {
			fwrite($file,$string2write);
		}
		 
	}
	
    
    
 	/**
 	 * Code borrowed from Mage_Catalog_Block_Product_View_Attributes
 	 * 
 	 * @return additional data for product
 	 */
    public function getAdditionalData($product, array $excludeAttr = array())
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
    
    
    
    
    
}

?>