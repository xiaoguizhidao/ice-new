<?php
/**
 * Attributes box for ChannelAdvisor attributes mapping
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Edit_Attributes extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    public function __construct()
    {
        $this->setTemplate('bridgechanneladvisor/types/edit/attributes.phtml');
    }

    /**
     * Preparing global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('bridgechanneladvisor')->__('Add New Attribute'),
                    'class' => 'add',
                    'id'    => 'add_new_attribute',
                    'on_click' => 'caContentAttribute.add()'
                ))
        );
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('bridgechanneladvisor')->__('Remove'),
                    'class' => 'delete delete-product-option',
                    'on_click' => 'caContentAttribute.remove(event)'
                ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Get attributes select field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'cacontent_attribute';
    }

    /**
     * Get attributes select field name
     *
     * @return string
     */
    public function getFieldName ()
    {
        return 'attributes';
    }

    /**
     * Build HTML code for select element which contains all uploaded from ChannelAdvisor attributes
     *
     * @return string
     */
    public function getCaContentAttributesSelectHtml()
    {
        $options = Mage::helper('bridgechanneladvisor')->getAttributesByCategoryId($this->getCaCategory());

        $select = $this->getLayout()->createBlock('bridgechanneladvisor/adminhtml_types_edit_select')
            ->setId($this->getFieldId().'_{{index}}_caattribute')
            ->setName($this->getFieldName().'[{{index}}][cacontent_attribute]')
            ->setOptions($options);

        return $this->_toOneLineString($select->toHtml());
    }

    /**
     * Build HTML select element of attribute set attributes
     *
     * @param boolean $escapeJsQuotes
     * @return string
     */
    public function getAttributesSelectHtml($escapeJsQuotes = false)
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setId($this->getFieldId().'_{{index}}_attribute')
            ->setClass('mage_attr_value')
            ->setName($this->getFieldName().'[{{index}}][attribute_id]')
            ->setOptions($this->_getAttributes($this->getAttributeSetId(), $escapeJsQuotes));
        return $select->getHtml();
    }

    /**
     * Get HTML code for button "Add New Attribute"
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Get HTML code for button "Remove"
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Get attributes of an attribute set
     *
     * @param int $setId
     * @param boolean $escapeJsQuotes
     * @return array
     */
    public function _getAttributes($setId, $escapeJsQuotes = false)
    {
        $attributes = Mage::getModel('bridgechanneladvisor/attribute')->getAllowedAttributes($setId);
        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $result[$attribute->getAttributeId()] = $escapeJsQuotes
                ? $this->jsQuoteEscape($attribute->getFrontendLabel())
                : $attribute->getFrontendLabel();
        }
        return $result;
    }

    /**
     * Encode the mixed $data into the JSON format
     *
     * @param mixed $data
     * @return string
     */
    protected function _toJson($data)
    {
        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Format string to one line, cut symbols \n and \r
     *
     * @param string $string
     * @return string
     */
    protected function _toOneLineString($string)
    {
        return str_replace(array("\r\n", "\n", "\r"), "", $string);
    }

}
