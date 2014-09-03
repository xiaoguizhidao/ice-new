<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Block to provide specific country select functional for multiple selects
 */
class Oro_Ice_Block_Adminhtml_System_Config_Specificcountry extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html =  $element->getElementHtml();

        $htmlId = $element->getHtmlId();
        $disabled = $element->getValue()? 'false' : 'true';

        $html .= "
        <script type='text/javascript'>
            Event.observe(document, 'dom:loaded', function () {
                var countrySelect_{$htmlId} = $('{$htmlId}'.replace(/allow/, ''));
                if (countrySelect_{$htmlId}) {
                    var disabled = {$disabled};
                    if ($('{$htmlId}'.replace(/allow/, '') + '_inherit')) {
                        disabled = disabled || $('{$htmlId}'.replace(/allow/, '') + '_inherit').checked;
                    }
                    countrySelect_{$htmlId}.disabled = disabled;

                    Event.observe($('{$element->getHtmlId()}'), 'change', function (ev) {
                        countrySelect_{$htmlId}.disabled = ev.srcElement.value == '0';
                    });
                }
            });
        </script>
        ";

        return $html;
    }


}
