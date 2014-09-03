<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Block_Adminhtml_Promo_Quote_Edit_Tab_Import
 */
class Oro_Friends_Block_Adminhtml_Promo_Quote_Edit_Tab_Import
    extends Mage_Adminhtml_Block_Widget_Form
    implements  Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Import Coupons');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return Mage::registry('current_promo_quote_rule') && Mage::registry('current_promo_quote_rule')->getId();
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        /** @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $this->__('Import'),
                'onclick'   => "$('edit_form').action = '". $this->getUrl('*/oro_friends_promo_quote/import')."'; $('edit_form').submit()",
            ));

        $this->setChild('import_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('enctype' =>  'multipart/form-data'));
        $form->setUseContainer(false);
        $this->setForm($form);

        $fieldSet = $form->addFieldset('import_csv', array('legend' => $this->__('Import Coupons')));

        $fieldSet->addField('delete_exists', 'select', array(
            'name'     => 'delete_exists',
            'label'    => $this->__('Delete Existing Coupons'),
            'title'    => $this->__('Delete Existing Coupons'),
            'required' => false,
            'options'  => array(
                0 => $this->__('No'),
                1 => $this->__('Yes'),
            )
        ));

        $fieldSet->addField('datafile', 'file', array(
            'name'     => 'datafile',
            'label'    => $this->__('CSV File'),
            'title'    => $this->__('CSV File'),
            'required' => false,
            'note'     => $this->__('Your server PHP settings allow you to upload files not more than %s at a time. Please modify post_max_size (currently is %s) and upload_max_filesize (currently is %s) values in php.ini if you want to upload larger files.', $this->getDataMaxSize(), $this->getPostMaxSize(), $this->getUploadMaxSize()),
        ));

        $fieldSet->addField('import_button', 'note', array(
            'text' => $this->getChildHtml('import_button'),
        ));

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getDataMaxSize()
    {
        return min($this->getPostMaxSize(), $this->getUploadMaxSize());
    }

    /**
     * @return string
     */
    public function getPostMaxSize()
    {
        return ini_get('post_max_size');
    }

    /**
     * @return string
     */
    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }
}
