<?php
/**
 * Adminhtml ChannelAdvisor Shipping Mapping form block
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Ship_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'bridgechanneladvisor';
        $this->_controller = 'adminhtml_ship';
        $this->_mode = 'edit';
        $model = Mage::registry('current_ship_type');
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Save Mapping'));
        $this->_updateButton('save', 'id', 'save_button');
        //$this->_updateButton('delete', 'label', $this->__('Delete Mapping'));
        if($model->getShipTypeId()){
            //$this->_removeButton('delete');
            $this->_addButton('delete', array(
                'label'   => Mage::helper('adminhtml')->__('Delete Mapping'),
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    . '\', \'' . Mage::helper('adminhtml')->getUrl('*/*/delete', array('ship_type_id' => $model->getShipTypeId())) . '\')',
                'class'   => 'scalable delete',
                'level'   => -1
            ));
        }
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if(!is_null(Mage::registry('current_ship_type')->getShipTypeId())) {
            return $this->__('Edit shipping method mapping');
        } else {
            return $this->__('New shipping method mapping');
        }
    }

    /**
     * Get css class name for header block
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }

}
