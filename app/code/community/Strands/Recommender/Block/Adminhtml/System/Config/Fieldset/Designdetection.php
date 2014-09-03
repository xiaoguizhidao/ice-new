<?php


class Strands_Recommender_Block_Adminhtml_System_Config_Fieldset_Designdetection
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

	protected $_template = 'strands/recommender/system/config/fieldset/javascript.php';
	
	
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $fieldset
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {    	
    	
    	$id = $element->getHtmlId();
    	
    	$coreResource = Mage::getSingleton('core/resource');
		$read = $coreResource->getConnection('core_read');
		$config_data = $coreResource->getTableName ('core_config_data');
		$termDB = '"design/package/name"'; //
		$sql = "SELECT value FROM $config_data WHERE path=$termDB";
		$packageDB = $read->fetchAll($sql);
		if (count($packageDB) == 0)
			$package = 'default';
		elseif ($packageDB[0]['value'] !== '')
			$package = $packageDB[0]['value'];
		else 
			$package = 'default';		
		
		$termDB = '"design/theme/default"';
		$sql = "SELECT value FROM $config_data WHERE path=$termDB";
		$themesDB = $read->fetchAll($sql);
		
		if (count($themesDB) == 0){
			$themes = 'default';
		} else{
			$themes = $themesDB[0]['value'];
			if ($themes == '' || $themes == null ){
				$themes = 'default';
			}
		}
		
			
		/*
		if (count($themesDB) == 0)
			$themes = 'default';
		elseif ($themesDB[0]['value'] !== '')
			$themes = $themesDB[0]['value'];
		else
			$themes = 'default';
    	*/
/*    	
    	$package = Mage::getStoreConfig('design/package/name');
    	if ($package == '') 
    		$package = 'default';
		$themes = Mage::getStoreConfig('design/theme/default');
		if ($themes == '')
			$themes = 'default';
*/    	
		$help = '<a href="'.$this->getUrl('recommender/help/widgets').'" target="_blank">here</a>';
    	
    	$msg = '<p>It has been detected that your Magento shop is using the following Design: <br>
    	<pre style="text-align: left; overflow: auto; margin:10px 10px;"><strong>Package:</strong> '.$package.'<br><strong>Theme:</strong> '.$themes.'<br></pre>
    	If you ever change this Design, you will have to return to this screen and save the configuration again. </p>
    	<p style="padding-top:5px">In case you need help, you can take a look '.$help.'.</p>';
    	$html = 
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px;" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
';    	
    	
    	
    	return $html.$this->toHtml(); 		
    }

}


?>