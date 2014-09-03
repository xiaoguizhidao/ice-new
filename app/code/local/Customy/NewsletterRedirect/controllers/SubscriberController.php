<?php
/**
 * Customy
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Customy EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.customy.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@customy.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.customy.com/ for more information
 * or send an email to sales@customy.com
 *
 * @copyright  Copyright (c) 2011-2013 Orot Technologies s.r.o.
 * @license    http://www.customy.com/LICENSE-1.0.html
 */
 
 include_once("Mage/Newsletter/controllers/SubscriberController.php");  
 class  Customy_NewsletterRedirect_SubscriberController extends Mage_Newsletter_SubscriberController
{
    
	/**
     * Set referer url for redirect in response
     *
     * @param   string $defaultUrl
     * @return  Mage_Core_Controller_Varien_Action
     */
    protected function _redirectReferer($defaultUrl=null)
    {
		if (Mage::getStoreConfig("newsletterredirect/settings/enabled")){
			//relative redirect url
			$redirectUrl = Mage::getStoreConfig("newsletterredirect/settings/redirect_url");
			$webBaseUrl = Mage::getStoreConfig("web/unsecure/base_url");
			$webBaseUrlStrLen = strlen($webBaseUrl);
			//if last char is / then remove it
			if (substr($webBaseUrl,-1) == '/' ){
				$webBaseUrl = substr($webBaseUrl,0,$webBaseUrlStrLen-1);
			}
			
			$redirectUrl = $webBaseUrl . $redirectUrl;
			
			$this->getResponse()->setRedirect($redirectUrl);
			return $this;
		}else{
        
			//original code
			$refererUrl = $this->_getRefererUrl();
			if (empty($refererUrl)) {
				$refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
			}

			$this->getResponse()->setRedirect($refererUrl);
			return $this;
		}
    }
}
