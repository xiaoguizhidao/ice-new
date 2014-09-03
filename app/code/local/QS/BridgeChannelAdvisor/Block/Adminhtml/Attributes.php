<?php
/**
 * Adminhtml ChannelAdvisor Content Item Types Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Attributes extends Mage_Adminhtml_Block_Widget_Grid_Container

{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('bridgechanneladvisor/upload.phtml');
    }

    /**
     * Preparing layout
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Items
     */
    protected function _prepareLayout()
    {
        $this->setChild('attributes', $this->getLayout()->createBlock('bridgechanneladvisor/adminhtml_attributes_grid'));

        $this->setChild('upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('bridgechanneladvisor')->__('Get Attributes'),
                    'class' => 'add',
                    'id'    => 'get_data_from_ca',
                    'on_click' => 'cadata.GetData()'
                ))
        );

        $this->setChild('upload_shipping_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('bridgechanneladvisor')->__('Get Shipping Methods'),
                    'class' => 'add',
                    'id'    => 'get_ship_from_ca',
                    'on_click' => 'cadata.GetShip()'
                ))
        );

        $this->setChild('import_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('bridgechanneladvisor')->__('Import Products'),
                    'class' => 'add',
                    'id'    => 'import_products_btn',
                    'on_click' => 'cadata.ImportProd()'
                ))
        );

        return $this;
    }

    /**
     * Get Upload Button
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildHtml('upload_button');
    }

    /**
     * Get Ship Button
     *
     * @return string
     */
    public function getShipButtonHtml()
    {
        return $this->getChildHtml('upload_shipping_button');
    }

    /**
     * Get Import Button
     *
     * @return string
     */
    public function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }

}
