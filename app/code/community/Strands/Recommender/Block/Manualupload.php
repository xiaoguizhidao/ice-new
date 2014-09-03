<?php 


class Strands_Recommender_Block_Manualupload extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) // Render a button to prompt up the manual upload window (see CatalogController)
    {
    	
        $this->setElement($element);
        $url = $this->getUrl('recommender/adminhtml_catalog/manualupload'); //

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Upload!')
                    ->setOnClick("StrandsRecommenderConfig.trackUploadButton('$url')")
                    ->toHtml();
        
        return $html;
    }
}

?>
