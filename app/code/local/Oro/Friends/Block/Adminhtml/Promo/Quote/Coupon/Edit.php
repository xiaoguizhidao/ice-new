<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Friends_Block_Adminhtml_Promo_Quote_Coupon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Initialize form
     * Add standard buttons
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'oro_friends';
        $this->_controller = 'adminhtml_promo_quote_coupon';

        parent::__construct();
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $coupon = Mage::registry('coupon_data');
        if ($coupon && $coupon['coupon_id']) {
            return Mage::helper('adminhtml')->__("Edit Coupon '%s'", $this->escapeHtml($coupon['coupon_id']));
        }
        else {
            return Mage::helper('adminhtml')->__('New Coupon');
        }
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/promo_quote/edit', array('id' => Mage::getSingleton('adminhtml/session')->getRuleId()));
    }
}
