<?php
class QS_BridgeChannelAdvisor_Model_Payment_Pay extends Mage_Payment_Model_Method_Abstract
{
    /**
     * unique internal payment method identifier
     */
    protected $_code = 'channeladvisor_payment';

    /**
     * this should probably be true if you're using this
     * method to take payments
     */
    protected $_isGateway               = true;

    /**
     * can this method authorise?
     */
    protected $_canAuthorize            = false;

    /**
     * can this method capture funds?
     */
    protected $_canCapture              = false;

    /**
     * can we capture only partial amounts?
     */
    protected $_canCapturePartial       = false;

    /**
     * can this method refund?
     */
    protected $_canRefund               = false;

    /**
     * can this method void transactions?
     */
    protected $_canVoid                 = false;

    /**
     * can admins use this payment method?
     */
    protected $_canUseInternal          = false;

    /**
     * show this method on the checkout page
     */
    protected $_canUseCheckout          = false;

    /**
     * available for multi shipping checkouts?
     */
    protected $_canUseForMultishipping  = false;

    /**
     * can this method save cc info for later use?
     */
    protected $_canSaveCc = false;

}
?>