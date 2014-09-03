<?php


Class Strands_Recommender_HelpController extends Mage_Core_Controller_Front_Action
{

	
	public function widgetsAction() 
	{
		$output = 		
'
<h1><a name="step5"></a>Displaying recommendations on your site</h1>
<p>
You can integrate Strands Recommender widgets in five different pages: homepage, product pages, category pages, shopping cart page and checkout page.
</p>
<p>You can decide to let the Strands Recommender Module insert your widgets automatically in your pages or insert them yourself easily following one of the manual methods.</p>
<h3>Quick & Easy - automatic insertion</h3>
<p>
For each one of the pages where you want to insert recommendation widgets, select <strong>quick & easy - automatic insertion</strong>.</p>
<p>If you leave the Block field empty, the recommendation widget will be added just before the body end of your html page. You can introduce the name of the Block, such as top.menu, left, right, content, footer, after_body_start, before_body_end. 
These Blocks must be present in your current Design configuration.</p>
<p>If you have entered a Block, you have to decide the relative position of the recommendation widget respect to the Block. It can be either before or after the Block. 
Depending on your Magento shop version, this setting might not work and the widgets would be placed after the specified Block.
</p>
<p>That is all, the recommendations widget should appear in your site! In case you experience any problems, please contact us at <a href="mailto:support-sbs@strands.com?subject=Magento widgets automatic problem" target="_blank">support-sbs@strands.com</a></p>
<p>
If you decide not to automatically insert widgets, here are two other methods available for adding recommendation widgets to your site.
Please choose which best fits your particular Magento shop installation:</p>
<h3>Manual - Insertion via file</h3>
<ol>
<li> For the corresponding page, select <strong>manual - insertion via file</strong>.</li>
<li> Locate the template file where you would like to add the widget. This may be different depending on the template you have in use for your site. For most of templates, you can find the template file in</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto;">app/design/frontend/[your_package]/[your_theme]/template/</pre>
</div>

<table>
<tbody>
<tr>
<td><strong>Page</strong></td>
<td><strong>Location in template/ directory</strong></td>
</tr>
<tr>
<td>Product Page</td>
<td>catalog/product/view/media.phtml</td>
</tr>
<tr>
<td>Category Page</td>

<td>catalog/category/view.phtml</td>
</tr>
<tr>
<td>Checkout Success</td>
<td>checkout/success.phtml</td>
</tr>
<tr>
<td>Checkout Success</td>
<td>checkout/multishipping/success.phtml</td>
</tr>
<tr>
<td>Basket Page</td>

<td>checkout/cart.phtml</td>
</tr>
</tbody>
</table>
<br>
<li> Place the following code where you want the widget to appear. Please, ensure that the code is inside &lt;?php ?&gt; tags, otherwise the widget won&#8217;t be shown:</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto;">echo $this-&gt;getLayout()-&gt;createBlock(\'recommender/widget\')-&gt;toHtml();</pre>

</div>
<li> Add the following code at the very bottom of the file (if possible at the very bottom of the corresponding page). Please, ensure that the code is inside &lt;?php ?&gt; tags, otherwise the widget won&#8217;t be shown :</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto;">echo $this-&gt;getLayout()-&gt;createBlock(\'recommender/js\')-&gt;toHtml();</pre>
</div>
<li> Do not forget to save your Strands Recommender Config.</li>
</ol>
<p>That is all, the recommendations widget is integrated! In case you experience any problems, please contact us at <a href="mailto:support-sbs@strands.com?subject=Magento widgets template problem" target="_blank">support-sbs@strands.com</a></p>

<h3>Manual - Insertion via CMS pages</h3>
<p>This is only available for CMS PAGES, such as homepage. Simply follow the steps below:</p>
<ol>
<li> For the corresponding page, select <strong>manual - insertion via CMS pages</strong>.</li>
<li> Log into the Magento Dashboard.</li>
<li> Go to CMS -&gt; Pages</li>

<li> Select the page where you want to add the widget.</li>
<li> Click on Content and an editor appears on the right side. This represents your page.</li>
<li> Add the following code wherever you want the widget to appear:</li>

<div class="box_cod">
<pre style="text-align: left; overflow: auto;">{{block type="recommender/widget" name="recommender.widget" }}</pre>
</div>
<li> At the very bottom of the page you have to add the following:</li>
<div class="box_cod">
<pre style="text-align: left; overflow: auto;">{{block type="recommender/js"}}</pre>
</div>
<li> Finally click on &#8216;Save Page&#8217; to store your changes.</li>
<li> Do not forget to save your Strands Recommender Config.</li>
</ol>

<p>That is all, the recommendations widget is integrated! In case you experience any problems, please contact us at <a href="mailto:support-sbs@strands.com?subject=Magento widgets CMS problem" target="_blank">support-sbs@strands.com</a></p>
<p>Follow us on <a href="http://twitter.com/strandsrecs" target="_blank">Twitter.com/StrandsRecs</a> to stay up-to-date with the latest news and updates from Strands Recommender.</p>
';		

		
		$this->getResponse()->setBody($output);

	}
	
	
	public function cronAction() 
	{
		$output = 		
'
<h1><a name="step4"></a>Cron setup for your Magento shop</h1>
<p>If you want to use "scheduled upload using Cron" for uploading your catalog to Strands Recommender, you must have the Magento Cron ready. 
You can do so by setting up a cron job using the configuration interface provided to you by your web hosting provider.</p>
<p>Most Magento shop installations have this set-up already. In case yours does not, you can set it up as follows:</p>
<p>Open a terminal to your web hosting account and execute the following command:</p>

<p>crontab -e</p>
<p>This will open the file where you can configure the corresponding cron job.</p>
<p>If other Magento cronjobs are already activated, the cronjob for Magento must be already activated and you will see something similar as below (you will not have to do anything):</p>
<div class="box_cod">
<pre style="text-align: left; overflow: auto;"># m h dom mon dow command
* * * * * /usr/bin/php /var/www/magento/cron.php</pre>
</div>
<p>In case you do not have this entry, it means there are no recurring Magento routines running on your webhosting account. Therefore, you have to create it. </p>
<p>In the line above, the asterisks define the recurrence (it must be set as * * * * *), /user/bin/php refers to the location of your php configuration (note: this could be located in a different path), and /var/www/magento/cron.php refers to the location of your Magento installation (this could also differ).</p>
<p>If you are unsure how to do set up a cron job of your own, please contact the web hosting provider for your Magento shop.</p>
<p>For more information about setting up a crontab, please refer to <a href=\'http://en.wikipedia.org/wiki/Cron\' onclick=\'javascript:_gaq.push([\'_trackEvent\',\'outbound-article\',\'en.wikipedia.org\']);\' target="_blank">Cron</a>.</p>

<p><!--<br />
<p>Follow us on <a href="http://twitter.com/strandsrecs" target="_blank">Twitter.com/StrandsRecs</a> to stay up-to-date with the latest news and updates from Strands Recommender.</p>
';		

		
		$this->getResponse()->setBody($output);

	}
	


    
}

?>



