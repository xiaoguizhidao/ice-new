<?php


class Strands_Recommender_Block_Adminhtml_System_Config_Fieldset_Insertionmode
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
    	
    	if ($id == 'recommender_widgets-homepage_file') {
    		$type = 'file';
    		$page = 'homepage';
    		$template = 'sample/home.phtml';
    	} elseif ($id == 'recommender_widgets-homepage_cms') {
    		$type = 'cms';
    		$page = 'homepage';
    	} elseif ($id == 'recommender_widgets-product_file') {
    		$type = 'file';
    		$page = 'product page';
    		$template = 'catalog/product/view/media.phtml';
    		$msg = 'file';
    	} elseif ($id == 'recommender_widgets-product_cms') {
    		$type = 'cms';
    		$page = 'product page';
    	} elseif ($id == 'recommender_widgets-category_file') {
    		$type = 'file';
    		$page = 'category page';
    		$template = 'catalog/category/view.phtml';
    		$msg = 'file';
    	} elseif ($id == 'recommender_widgets-category_cms') {
    		$type = 'cms';
    		$page = 'category page';
    	} elseif ($id == 'recommender_widgets-cart_file') {
    		$type = 'file';
    		$page = 'shopping-cart page';
    		$template = 'checkout/cart.phtml';
    		$msg = 'file';
    	} elseif ($id == 'recommender_widgets-cart_cms') {
    		$type = 'cms';
    		$page = 'shopping-cart page';
    	} elseif ($id == 'recommender_widgets-checkout_file') {
    		$type = 'file';
    		$page = 'checkout page';
    		$template = 'checkout/success.phtml';
    		$msg = 'file';
    	} elseif ($id == 'recommender_widgets-checkout_cms') {
    		$type = 'cms';
    		$page = 'checkout';    		
    	}
    	
    	if ($type == 'file') {
			$html =
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px;" colspan="4">'.
'<strong>Instructions for setting recommendation widgets in '.$page.'</strong><br><br>
<p>Simply follow the steps below.</p>
<ol style="list-style:decimal; padding-left:20px;">
<li> Locate the template file where you would like to add the widget. This may be different depending on the template you have in use for your site. 
In the default case, you can find the template file in:</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto; margin:10px 10px;">app/design/frontend/[your_package]/[your_theme]/template/<strong>'.$template.'</strong></pre>
</div>
<li> Place the following code where you want the widget to appear. Please, ensure that the code is inside &lt;?php ?&gt; tags, otherwise the widget won&#8217;t be shown:</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto; margin:10px 10px;">echo $this-&gt;getLayout()-&gt;createBlock(\'recommender/widget\')-&gt;toHtml();</pre>
</div>
<li> Add the following code at the very bottom of the file (if possible at the very bottom of the corresponding page). 
Please, ensure that the code is inside &lt;?php ?&gt; tags, otherwise the widget won&#8217;t be shown:</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto; margin:10px 10px;">echo $this-&gt;getLayout()-&gt;createBlock(\'recommender/js\')-&gt;toHtml();</pre>
</div>
<li> Do not forget to save your Strands Recommender Config.</li>
</ol>
<p>That is all, the recommendations widget is integrated! 
In case you experience any problems, please contact us at <a href="mailto:support-sbs@strands.com?subject=Magento widgets template problem" target="_blank">support-sbs@strands.com</a></p>
'				
.'			</td>
		</tr>
';		
    	} elseif ($type == 'cms') {
			$html =
'		<tr id="row_'.$id.'">
			<td style="padding-left:5px;" colspan="4">'.
'<strong>Instructions for setting recommendation widgets in '.$page.'</strong><br><br>	
<p>This is only available for CMS PAGES, such as homepage.</p>
<p>Simply follow the steps below.</p>
<ol style="list-style:decimal; padding-left:20px;">
<li> Go to CMS -&gt; Pages</li>
<li> Select the page where you want to add the widget.</li>
<li> Click on Content and an editor appears on the right side. This represents your page.</li>
<li> Add the following code wherever you want the widget to appear:</li>
<div class="box_cod">
<pre style="text-align:left; overflow:auto; margin:10px 0">{{block type="recommender/widget" name="recommender.widget" }}</pre>
</div>
<li> At the very bottom of the page you have to add the following:</li>
<div class="box_cod">
<pre style="text-align:left; overflow:auto; margin:10px 0">{{block type="recommender/js"}}</pre>
</div>
<li>Finally click on &#8216;Save Page&#8217; to store your changes</li>
<li> Do not forget to save your Strands Recommender Config.</li>
</ol>
<p>That is all, the recommendations widget is integrated! In case you experience any problems, please contact us at <a href="mailto:support-sbs@strands.com?subject=Magento widgets CMS problem" target="_blank">support-sbs@strands.com</a></p>
'			
.'			</td>
		</tr>
';					
    	}

    	
    	return $html; 		
    }

}


?>