<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */


class Oro_Delmar_Block_Adminhtml_Sales_Order_Grid extends Directshop_FraudDetection_Block_Adminhtml_Sales_Order_Grid
{

    public function _prepareMassaction()
    {
        parent::_prepareMassaction();

        if (Mage::getSingleton('admin/session')->isAllowed('admin/delmar/shipment/approve')) {
            $this->getMassactionBlock()->addItem('approve_shipment', array(
                'label'=> Mage::helper('sales')->__('Add to Delmar shipping queue'),
                'url'  => $this->getUrl('delmar/shipment/massApprove'),
            ));
        }

        return $this;
    }

}
