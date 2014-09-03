<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Friends_Block_Adminhtml_Promo_Quote_Coupon_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_quote_form_coupon');
        $this->setTitle(Mage::helper('salesrule')->__('Coupon Edit'));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/oro_friends_promo_coupon/save'),
            'method'  => 'post',
            'enctype' =>  'multipart/form-data',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldSet = $form->addFieldset('edit_coupon', array('legend' => $this->__('Edit Coupon')));

        $fieldSet->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => $this->__('Coupon Code'),
            'title'    => $this->__('Coupon Code'),
            'required' => true
        ));

        $fieldSet->addField('usage_limit', 'text', array(
            'name'     => 'usage_limit',
            'label'    => $this->__('Uses per Coupon'),
            'title'    => $this->__('Uses per Coupon'),
            'required' => false
        ));

        $fieldSet->addField('usage_per_customer', 'text', array(
            'name'     => 'usage_per_customer',
            'label'    => $this->__('Uses per Customer'),
            'title'    => $this->__('Uses per Customer'),
            'required' => false
        ));

        $form->addField('coupon_id', 'hidden', array(
            'name' => 'coupon_id'
        ));


        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        $form = $this->getForm();

        $adminSession = Mage::getSingleton('adminhtml/session');
        if ($adminSession->getData('coupon_data')) {
            $form->setValues($adminSession->getData('coupon_data'));
            $adminSession->getData('coupon_data', null);
        } elseif (Mage::registry('coupon_data')) {
            $form->setValues(Mage::registry('coupon_data'));
        }

        return parent::_initFormValues();
    }

}
