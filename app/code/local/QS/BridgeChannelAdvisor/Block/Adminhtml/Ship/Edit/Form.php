<?php
/**
 * Adminhtml ChannelAdvisor Shipping Methods mapping form block
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Ship_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Ship_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $shipItemType = $this->getShipType();

        $fieldset = $form->addFieldset('content_fieldset', array(
            'legend'    => $this->__('Shipping Carrier mapping')
        ));

        $shipSelect = $this->getMageShipmentSelectElement()
            ->setValue($shipItemType->getMageCarrierCode());

        $fieldset->addField('attribute_set', 'note', array(
            'label'     => $this->__('Shipping Carrier'),
            'title'     => $this->__('Shipping Carrier'),
            'required'  => true,
            'text'      => '<div id="shipping_carrier_select">' . $shipSelect->toHtml() . '</div>',
        ));

        $caCarriers = $this->getCaShipCarrier();
        $fieldset->addField('select_ca_carrier', 'select', array(
            'label'     => $this->__('ChannelAdvisor Carrier'),
            'title'     => $this->__('ChannelAdvisor Carrier'),
            'required'  => true,
            'name'      => 'carrier_id',
            'options'   => $caCarriers,
            'value'     => $shipItemType->getCarrierId(),
        ));

        $form->addValues($shipItemType->getData());
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
    public function getCaShipCarrier()
    {
        $caShipCollection = Mage::getModel('bridgechanneladvisor/ship')->getCollection();
        $result = array('' => '');
        $model = Mage::registry('current_ship_type');
        $checkCarr = array();
        $carriersAlreadyUsed = Mage::getModel('bridgechanneladvisor/shiptype')->getCollection();

        if(!$model->getId()){
            foreach($carriersAlreadyUsed as $carrierAlreadyUsed){
                $checkCarr[$carrierAlreadyUsed->getCarrierId()] = $carrierAlreadyUsed->getCarrierId();
            }
        }else{
            foreach($carriersAlreadyUsed as $carrierAlreadyUsed){
                if($carrierAlreadyUsed->getCarrierId() != $model->getCarrierId()){
                    $checkCarr[$carrierAlreadyUsed->getCarrierId()] = $carrierAlreadyUsed->getCarrierId();
                }
            }
        }

        foreach($caShipCollection as $ship){
            if(!in_array($ship->getCarrierId(), $checkCarr)){
                $result[$ship->getCarrierId()] = ucwords($ship->getCarrierCode()).' - '.ucwords($ship->getClassCode());
            }
        }

        return $result;
    }

    /**
     * Get Select field with list of available attribute sets
     *
     * @return Varien_Data_Form_Element_Select
     */
    public function getMageShipmentSelectElement()
    {
        $field = new Varien_Data_Form_Element_Select();
        $field->setName('mage_carrier_code')
            ->setId('select_carrier')
            ->setForm(new Varien_Data_Form())
            ->addClass('required-entry')
            ->setValues($this->_getMageShipmentArray());
        return $field;
    }

    /**
     * Get array with magento shipping methods
     *
     * @return array
     */
    protected function _getMageShipmentArray()
    {
        $carriers = Mage::getStoreConfig('carriers', Mage::app()->getStore()->getId());
        $result['null'] = ' ';
        $model = Mage::registry('current_ship_type');
        $checkCarr = array();
        $carriersAlreadyUsed = Mage::getModel('bridgechanneladvisor/shiptype')->getCollection();

        if(!$model->getId()){
            foreach($carriersAlreadyUsed as $carrierAlreadyUsed){
                $checkCarr[$carrierAlreadyUsed->getMageCarrierCode()] = $carrierAlreadyUsed->getMageCarrierCode();
            }
        }else{
            foreach($carriersAlreadyUsed as $carrierAlreadyUsed){
                if($carrierAlreadyUsed->getMageCarrierCode() != $model->getMageCarrierCode()){
                    $checkCarr[$carrierAlreadyUsed->getMageCarrierCode()] = $carrierAlreadyUsed->getMageCarrierCode();
                }
            }
        }

        foreach ($carriers as $carrierCode => $carrierConfig){
            if(!in_array($carrierCode, $checkCarr)){
                $result[$carrierCode] = ucwords($carrierCode);
            }
        }


        return $result;
    }

    /**
     * Get current attribute set mapping from register
     *
     * @return QS_BridgeChannelAdvisor_Model_Type
     */
    public function getShipType()
    {
        return Mage::registry('current_ship_type');
    }

    /**
     * Get URL for saving the current map
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('ship_type_id' => $this->getShipType()->getShipTypeId()));
    }
}
