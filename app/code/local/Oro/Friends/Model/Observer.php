<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Model_Observer
 */
class Oro_Friends_Model_Observer
{
    /**
     * Initialize loyalty customer token
     *
     * @param Varien_Event_Observer $observer
     */
    public function loyaltyAuth(Varien_Event_Observer $observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getData('model');
        $token    = Mage::helper('oro_friends/api')->getAuthToken($customer);
        $session  = Mage::getSingleton('customer/session');

        $session->setData('oro_friends_token', $token);

        Mage::helper('oro_friends/api')->sitevisitEvent($customer);
    }

    /**
     * On sales_order_place_after. Fire purchase Event to 500Friends API
     *
     * @param Varien_Event_Observer $observer
     */
    public function purchaseEvent(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $order = $observer->getOrder();
            Mage::helper('oro_friends/api')->purchaseEvent($order);
        }
    }

    /**
     * On sales_order_place_after. Fire purchase Event to 500Friends API
     *
     * @param Varien_Event_Observer $observer
     */
    public function subscribeEvent(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn() && $session->getCustomer()->getEmail() == $observer->getEmail()) {
            Mage::helper('oro_friends/api')->subscribeEvent($observer->getEmail());
        }
    }

    /**
     * Checks if coupon given as a query string parameter. On controller_action_predispatch
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkCoupon(Varien_Event_Observer $observer)
    {
        $coupon = $observer->getControllerAction()->getRequest()->getParam('coupon');

        if ($coupon && strlen($coupon)) {
            Mage::getSingleton('customer/session')->setExternalCoupon($coupon);
            $observer->getControllerAction()->getRequest()->setParam('coupon', null);
        }
    }

    /**
     * Applies Coupon from session to quote. On controller_action_predispatch_checkout
     *
     * @param Varien_Event_Observer $observer
     */
    public function applyCoupon(Varien_Event_Observer $observer)
    {
        $coupon = Mage::getSingleton('customer/session')->getExternalCoupon(true);
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if ($coupon) {
//            Mage::getSingleton('customer/session')->setExternalOldCoupon($quote->getCouponCode());
            $quote->setCouponCode($coupon);
        }
//        else {
//            $oldCoupon = Mage::getSingleton('customer/session')->getExternalOldCoupon(true);
//            if ($oldCoupon && !$quote->getCouponCode()) {
//                $quote->setCouponCode($coupon);
//            }
//        }
    }

}
