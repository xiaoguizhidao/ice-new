<?php


class Strands_Recommender_Block_Adminhtml_System_Config_Fieldset_Feasible
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $fieldset
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {    	
    	$warningCron = "ATTENTION! Cron seems to be disabled in your system. Scheduled upload using Cron will probably not work. Please try a different option.";
    	$warningFeed = "ATTENTION! It seems that your IP is not public. Automatic catalog feed will most surely not work. Please try a different option.";
    	$warning = "ATTENTION!";
    	
    	$id = $element->getHtmlId();
    	
    	if ($id == 'recommender_catalog_cron_feasible') { // Show $warningCron in the screen
    		$config = $this->getConfigData();
    		if (isset($config['recommender/cron/last_cron_action']))
    			$last_cron = $config['recommender/cron/last_cron_action'];
    		else 
    			$last_cron = 0;
    		$currentTime = Mage::getModel('core/date')->timestamp(time());
    		$timeDiff = $currentTime - $last_cron;

    		if ($timeDiff > intval('299')) {
    	    	$html = 
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:5px; padding-bottom:10px;" colspan="4"><span style="color:red" for="'.$id.'">'.$this->escapeHtml($warningCron).'</span></td>
		</tr>
';
    		} else {
    			$html = '';
    		}
    	} elseif ($id == 'recommender_catalog_feed_feasible') { // Show $warningFeed in the screen
    		$api = Mage::getSingleton('recommender/api');
    		$feedAvailable = $api->checkCatalogFeed();

    		if (!$feedAvailable) {
      	    	$html = 
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:5px; padding-bottom:10px;" colspan="4"><span style="color:red" for="'.$id.'">'.$this->escapeHtml($warningFeed).'</span></td>
		</tr>
';
    		} else {
    			$html = '';
    		}
    	} elseif ($id == 'recommender_catalog_strands_catalog_time') { // Show the last time the catalog was uploaded
        	$object = $element->getData();
    		$value = $object['value'];
    	
    		if ($value) {
    			$msg = 'The last time your catalog was uploaded to Strands Recommender was on <strong>'.$this->escapeHtml($value).'</strong><br><br>';
	    		$style = '';
    		} else {
    			$msg = 'The last time your catalog was uploaded to Strands Recommender was: <strong style="color:red">NEVER</strong>. (This field will not be updated unless you reload the page.)';
    			$style = '';
    		}
    	
    		$html = 
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:10px;" colspan="4"><span '.$style.' for="'.$id.'">'.$msg.'</span></td>
		</tr>
';    	
    	} elseif ($id == 'recommender_cron_help') { //Show help page to configure cron (see HelpController)
    		$url = $this->getUrl('recommender/help/cron');
    		$msg = 'If you need help to configure your Magento Cron, take a look <a href="'.$url.'" target="_blank">here</a>.<br><br>';
    		
    		
    		$html =
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:5px;" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
';
		} elseif ($id == 'recommender_catalog_cron_explanation') { // Show explanation of cron upload
    		$msg = 'The Strands Recommender module will synchronize your catalog using the internal magento scheduler (Cron) to periodically  send updates to the Strands Recommender backend. The periodicity of such updates can be defined below.';
    		$html =
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:5px;" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
';		
    	} elseif ($id == 'recommender_catalog_feed_explanation') { // Show explanation of feed upload
    		$msg ='The Strands Recommender module will offer a feed that will be accessed by the Strands Recommender servers at intervals that can be defined in your <a href="http://recommender.strands.com/catalog/import" target="_blank">Strands Recommender Dashboard</a>.';
    		$html =
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:5px;" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
'; 
    	} elseif ($id == 'recommender_catalog_manual_explanation') { // Show explanation of manual upload
    		$msg = 'No automatic catalog syncronization will happen. When pressing the button <strong>Upload!</strong> below, the catalog will be uploaded a single time.';
    		$html =
'		<tr id="row_'.$id.'">
   			<td style="padding-left:5px; padding-top:5px;" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
'; 
    	} elseif ($id == 'recommender_account_new') { // Place user to open a Strands Recommender account in case he doesn't has one
    		$msg = '* If you still do not have a Strands Recommender account, you can get your 30 day free trial <a href="http://recommender.strands.com/magento" target="_blank">here</a>.<br><br>';
			$html = 
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px; padding-top:5px" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
';    	
    	}
    	
    	return $html; 		
    }

}


?>