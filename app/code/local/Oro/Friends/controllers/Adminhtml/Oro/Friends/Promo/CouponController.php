<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Edit coupon controller
 */
class Oro_Friends_Adminhtml_Oro_Friends_Promo_CouponController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Render Edit Coupon form
     */
    public function editAction()
    {
        $couponId = $this->getRequest()->getParam('id');

        try {
            if (!$couponId) {
                Mage::throwException($this->__('No Coupon Code'));
            }

            $coupon = Mage::getModel('salesrule/coupon')->load($couponId);
            if (!$coupon->getId()) {
                Mage::throwException($this->__('No Coupon with id %s found', $couponId));
            }

            Mage::register('coupon_data', $coupon->getData());

            $ruleId = $this->getRequest()->getParam('rule');
            if ($ruleId) {
                Mage::getSingleton('adminhtml/session')->setRuleId($ruleId);
            }

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    /**
     * Save coupon
     */
    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        $data = $this->getRequest()->getPost();

        if ($data && !empty($data['coupon_id'])) {
            try {
                $coupon = Mage::getModel('salesrule/coupon')->load($data['coupon_id']);

                $coupon->addData($data);
                $coupon->save();

                $session->addSuccess($this->__('Coupon %s saved', $data['coupon_id']));
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $data['coupon_id']));
                return;
            }
        }

        $this->_redirect('*/promo_quote/edit', array('id' => $session->getRuleId()));
    }

    /**
     * Delete coupon
     */
    public function deleteAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        $couponId = $this->getRequest()->getParam('id');
        if ($couponId) {

            try {
                $coupon = Mage::getModel('salesrule/coupon')->load($couponId);

                if ($coupon->getId()) {
                    $coupon->delete();
                }

                $session->addSuccess($this->__('Coupon %s deleted', $couponId));
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $couponId));
                return;
            }
        }

        $this->_redirect('*/promo_quote/edit', array('id' => $session->getRuleId()));
    }
}
