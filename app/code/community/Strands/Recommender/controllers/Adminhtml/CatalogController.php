<?php

Class Strands_Recommender_Adminhtml_CatalogController extends Mage_Adminhtml_Controller_Action
{
	
	public function manualuploadAction()
	{
		
		$observer = Mage::getModel('recommender/observer');
		
		$flagActive = Mage::getStoreConfigFlag('recommender/account/active');
		$flagApid = Mage::getStoreConfigFlag('recommender/account/strands_api_id');
		$flagToken = Mage::getStoreConfigFlag('recommender/account/strands_customer_token');
		
		if (!$flagActive || !$flagApid || !$flagToken) {
			$output = 
	'<h1><strong>Your catalog could not be uploaded to Strands Recommender</strong></h1><br>
	<h1>Please save your configuration first and then try again.</h1>
	<br><br>
	You can <a href="JavaScript:window.close()">close</a> this windows now.';
		}
		
		else {
		
			Mage::getConfig()->saveConfig('recommender/manual/strands_catalog_upload_now',0);
			
			$params = $this->getRequest()->getPost();
			
			$result = $observer->ftpCatalog($params['login'],$params['password']);
			
			if ($result) {
				$output = '<h1><strong>Your catalog has been successfully uploaded to Strands Recommender.</strong><br></h1>You can <a href="JavaScript:window.close()">close</a> this windows now.';
				
				Mage::getConfig()->saveConfig('recommender/catalog/strands_catalog_time',date('D, d M Y H:i:s' , Mage::getModel('core/date')->timestamp(time())));
				
			} else {
				$localfile = "strandsCatalog.xml";
				$absPath = Mage::getBaseDir();
				$localpath = $absPath."/media/";
				
				if (file_exists("$localpath$localfile")) {
					$output = 
	'<h1><strong>Your catalog could not be uploaded to Strands Recommender.<br>Please upload it manually</strong></h1><br>
	Log into your <a href="https://recommender.mystrands.in/login/" target="_blank">Strands Recommender</a> account with your User Name and Password.<br>
	Navigate to Catalog > Import. <br>
	Go to "2. Upload file via browser". <br>
	Select the following file: <strong>'.$localpath.$localfile.'</strong> <br>
	Click Upload.';
				} else {
					$output = 
	'<h1><strong>Your catalog could not be uploaded to Strands Recommender.</strong></h1><br>There was an internal error.<br>
	Please contact with <a href="mailto:support-sbs@strands.com?subject=Magento catalog problem" target="_blank">support-sbs@strands.com</a>';
				}
	
			}
			
		}
		
		$this->getResponse()->setHeader('Content-Type', 'text/html')->setBody($output);
		
	}

}

?>