<?php


class Strands_Recommender_Block_Adminhtml_System_Config_Fieldset_Lookandfeel
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
    	
    	$id = $element->getHtmlId();
    	
    	$msg = 
'
<p>The look and feel as well as the logic for each one of the recommendation widgets can be adjusted from the
 <a href="http://recommender.strands.com/tpl/website/" target="_blank"> Strands Recommender Dashboard</a>.
</p>
<p>
You can take a look of your current settings <a href="http://recommender.strands.com/tpl/website/" target="_blank">here</a>.
</p>
'; 

    	
    	$html = 
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px;" colspan="4"><span for="'.$id.'">'.$msg.'</span></td>
		</tr>
';    	
    	
    	
    	return $html.$this->toHtml(); 		
    }

}


?>