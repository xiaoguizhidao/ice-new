<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Export_Edit_Tab_General
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

    /**
     * @return bool
     */
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

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Core_Block_Text $scriptBlock */
        $scriptBlock = $this->getLayout()->createBlock('core/text', 'form_after');
        $scriptBlock->setText('<script type="text/javascript">
        function toggleTab(tabId, flag) {
            true == flag ? $(tabId).parentNode.style.display = "block" : $(tabId).parentNode.style.display = "none";
        }</script>');
        $this->setChild('form_after', $scriptBlock);

        return parent::_prepareLayout();
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

        $fieldset->addField('export_selected', 'select', array(
            'label'    => $helper->__('Export Products'),
            'required' => true,
            'name'     => 'export_selected',
            'id'       => 'export_selected',
            'options'  => array (
                0 => $this->__('All'),
                1 => $this->__('Selected'),
            ),
            'onchange' => 'toggleTab(\'schedule_tabs_product_grid\', this.value)',
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
            'title' => $helper->__('Scheduled to Time'),
            'required' => true,
            'note' => "Current date and time: ".Mage::app()->getLocale()->date()->toString(Zend_Date::DATETIME_SHORT)."<br/>
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
