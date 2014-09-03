<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Block_Adminhtml_Field_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('amorderattach_field');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('amorderattach')->__('Attachment Field Details'), 'class' => 'fieldset'));

        if ($model->getId()) {
            $fieldset->addField('field_id', 'hidden', array(
                'name' => 'field_id',
            ));
        }
        
        $yn = array(
            array(
                'value' => '1',
                'label' => $this->__('Yes'),
            ),
            array(
                'value' => '0',
                'label' => $this->__('No'),
            ),
        );

        $codeParams = array(
            'name'      => 'code',
            'label'     => Mage::helper('amorderattach')->__('Alias'),
            'title'     => Mage::helper('amorderattach')->__('Alias'),
            'required'  => true,
            'note'      => Mage::helper('amorderattach')->__('Please use lowercase letters a-z and _ symbols only.'),
        );
        if ($model->getId())
        {
            $codeParams['disabled'] = 'disabled';
        }
        $fieldset->addField('code', 'text', $codeParams);
        
        if ($model->getId()) {
            $fieldset->addField('fieldname', 'text', array(
                'name'      => 'label',
                'label'     => Mage::helper('amorderattach')->__('Field Name'),
                'title'     => Mage::helper('amorderattach')->__('Field Name'),
                'disabled'  => 'disabled',
                'note'      => Mage::helper('amorderattach')->__('To be used in Email templates.'),
            ));
        }
        
        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => Mage::helper('amorderattach')->__('Label'),
            'title'     => Mage::helper('amorderattach')->__('Label'),
            'required'  => true,
        ));
        
        $typeParams = array(
            'name'      => 'type',
            'label'     => Mage::helper('amorderattach')->__('Field Type'),
            'title'     => Mage::helper('amorderattach')->__('Field Type'),
            'values'    => Mage::helper('amorderattach')->getTypes(false),
            'onchange'  => 'javascript: checkFieldType(this);',
            'after_element_html' => Mage::app()->getLayout()->createBlock('amorderattach/adminhtml_field_edit_options', 'field.options')->setField($model)->toHtml(),
        );
        
        if ($model->getId())
        {
            $typeParams['disabled'] = 'disabled'; 
            $fieldset->addField('hiddentype', 'hidden', array(
                'name'      => 'hiddentype',
            ));
            $model->setHiddenType($model->getType());
        }      
        
             
        
        $fieldset->addField('type', 'select', $typeParams);
        
        $fieldset->addField('default_value', 'text', array(
            'name'      => 'default_value',
            'label'     => Mage::helper('amorderattach')->__('Default Value'),
            'title'     => Mage::helper('amorderattach')->__('Default Value'),
            'note'		=> Mage::helper('amorderattach')->__('Will be automatically assigned to any new order placed.'),
        ));
        
        $fieldset->addField('customer_visibility', 'select', array(
            'name'      => 'customer_visibility',
            'label'     => Mage::helper('amorderattach')->__('Display To Customer'),
            'title'     => Mage::helper('amorderattach')->__('Display To Customer'),
            'values'    => Mage::helper('amorderattach')->getCustomerVisibility(),
        ));
        
        $fieldset->addField('is_enabled', 'select', array(
            'name'      => 'is_enabled',
            'label'     => Mage::helper('amorderattach')->__('Enabled'),
            'title'     => Mage::helper('amorderattach')->__('Enabled'),
            'values'    => $yn,
        ));
        
        $fieldset->addField('show_on_grid', 'select', array(
            'name'      => 'show_on_grid',
            'label'     => Mage::helper('amorderattach')->__('Show On Order Grid'),
            'title'     => Mage::helper('amorderattach')->__('Show On Order Grid'),
            'values'    => $yn,
        ));
        
        $values = array();
        foreach($model->getData() as $key=>$val){
           $values[$key] = $val;
        }
        $values['hiddentype'] = $model->getHiddenType();
        
        $form->setValues($values);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('amorderattach')->__('Field Information');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('amorderattach')->__('Field Information');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
}