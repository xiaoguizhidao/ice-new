<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Block_Adminhtml_Promo_Quote_Edit_Form
 */
class Oro_Friends_Block_Adminhtml_Promo_Quote_Edit_Form
    extends Mage_Adminhtml_Block_Promo_Quote_Edit_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getData('action'),
            'method'  => 'post',
            'enctype' =>  'multipart/form-data',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }
}
