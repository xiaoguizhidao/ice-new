<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Import_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('General Information');
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
        return true;
    }

    /** Adding fields to the edit form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);
        $helper = Mage::helper('oro_dataflow');
        $fieldset = $form->addFieldset(
            'general',
            array('legend' => $helper->__('General Settings'))
        );

        $fieldset->addField('file_path', 'file', array(
            'label'    => $helper->__('Export Products'),
            'required' => true,
            'name'     => 'file_path',
            'id'       => 'file_path',
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $fieldset->addField('scheduled_at_date', 'date', array(
            'name' => 'scheduled_at_date',
            'label' => $helper->__('Scheduled to Date'),
            'title' => $helper->__('Scheduled to Date'),
            'format' => $dateFormatIso,
            'required' => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        ));

        $fieldset->addField('scheduled_at_time', 'time', array(
            'name' => 'scheduled_at_time',
            'label' => $helper->__('Scheduled to Time'),
            'title' => $helper->__('Scheduled ti Time'),
            'required' => true,
            'note' => "Local date/time for timezone 'America/New_York'<br/>
Current date and time: ".Mage::app()->getLocale()->date()->toString(Zend_Date::DATETIME_SHORT)."<br/>
<strong>Please schedule big import/export operations at 3AM (at night and after maintenance window)</strong>"
        ));

        $adminSession = Mage::getSingleton('adminhtml/session');
        if ($adminSession->getData('task_data')) {
            $form->setValues($adminSession->getData('task_data'));
            $adminSession->getData('task_data', null);
        } elseif (Mage::registry('task_data')) {
            $form->setValues(Mage::registry('task_data')->getData());
        }

        return parent::_prepareForm();
    }
}
