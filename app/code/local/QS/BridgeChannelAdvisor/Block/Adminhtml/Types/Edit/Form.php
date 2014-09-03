<?php
/**
 * Adminhtml ChannelAdvisor Content types mapping form block
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Types_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $itemType = $this->getItemType();

        $fieldset = $form->addFieldset('content_fieldset', array(
            'legend'    => $this->__('Attribute set mapping')
        ));

        $attributeSetsSelect = $this->getAttributeSetsSelectElement()
            ->setValue($itemType->getAttributeSetId());
        if ($itemType->getAttributeSetId()) {
            $attributeSetsSelect->setDisabled(true);
        }

        $fieldset->addField('attribute_set', 'note', array(
            'label'     => $this->__('Attribute Set'),
            'title'     => $this->__('Attribute Set'),
            'required'  => true,
            'text'      => '<div id="attribute_set_select">' . $attributeSetsSelect->toHtml() . '</div>',
        ));

        $categories = $this->getCaCategories();
        $fieldset->addField('select_ca_category', 'select', array(
            'label'     => $this->__('ChannelAdvisor Classification'),
            'title'     => $this->__('ChannelAdvisor Classification'),
            'required'  => true,
            'name'      => 'ca_category',
            'options'   => $categories,
            'value'     => $itemType->getCategoryId(),
        ));

        $attributesBlock = $this->getLayout()
            ->createBlock('bridgechanneladvisor/adminhtml_types_edit_attributes');
        if ($itemType->getId()) {
            $attributesBlock->setAttributeSetId($itemType->getAttributeSetId())
                ->setAttributeSetSelected(true)
                ->setCaCategory($itemType->getCategoryId());
        }

        $attributes = Mage::registry('attributes');
        if (is_array($attributes) && count($attributes) > 0) {
            $attributesBlock->setAttributesData($attributes);
        }

        $fieldset->addField('attributes_box', 'note', array(
            'label'     => $this->__('Attributes Mapping'),
            'text'      => '<div id="attributes_details">' . $attributesBlock->toHtml() . '</div>',
        ));

        $form->addValues($itemType->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getSaveUrl());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get ChannelAdvisor categories array
     *
     * @return array
     */
    public function getCaCategories()
    {
        $categoryCollection = Mage::getModel('bridgechanneladvisor/category')->getCollection();
        $result = array('' => '');
        foreach($categoryCollection as $cat){
            $result[$cat->getId()] = ucwords($cat->getCategoryName());
        }

        return $result;
        /*$categoryCollection = Mage::getModel('bridgechanneladvisor/category')->getCollection();
                $usedCollection = Mage::getModel('bridgechanneladvisor/type')->getCollection();
                $result = array('' => '');
                foreach($categoryCollection as $cat){
                    $pin = 0;
                    if(count($usedCollection)>0){
                        foreach($usedCollection as $usedOnes){
                            if($cat->getId() == $usedOnes->getCategoryId()){
                                $pin = 1;
                                break;
                            }
                        }
                    }
                    if($pin == 0){
                        $result[$cat->getId()] = ucwords($cat->getCategoryName());
                    }
                } */
    }

    /**
     * Get Select field with list of available attribute sets
     *
     * @return Varien_Data_Form_Element_Select
     */
    public function getAttributeSetsSelectElement()
    {
        $field = new Varien_Data_Form_Element_Select();
        $field->setName('attribute_set_id')
            ->setId('select_attribute_set')
            ->setForm(new Varien_Data_Form())
            ->addClass('required-entry')
            ->setValues($this->_getAttributeSetsArray());
        return $field;
    }

    /**
     * Get array with attribute sets
     *
     * @return array
     */
    protected function _getAttributeSetsArray()
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId());

        $ids = array();
        $itemType = $this->getItemType();
        if ( !($itemType instanceof Varien_Object && $itemType->getId()) ) {
            $typesCollection = Mage::getResourceModel('bridgechanneladvisor/type_collection')->load();
            foreach ($typesCollection as $type) {
                $ids[] = $type->getAttributeSetId();
            }
        }

        $result = array('' => '');
        foreach ($collection as $attributeSet) {
            $result[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }

        return $result;
    }

    /**
     * Get current attribute set mapping from register
     *
     * @return QS_BridgeChannelAdvisor_Model_Type
     */
    public function getItemType()
    {
        return Mage::registry('current_item_type');
    }

    /**
     * Get URL for saving the current map
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('type_id' => $this->getItemType()->getId()));
    }
}
