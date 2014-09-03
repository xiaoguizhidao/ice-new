<?php

class Oro_Ice_AjaxController extends Mage_Core_Controller_Front_Action
{

    public function affirmPaymentsEnabledAction()
    {


        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $affirmAvailable = Mage::helper('ice/affirmpayments')->isAvailable($billingAddress);
        $response = array(
            'affirmAvailable' => $affirmAvailable
        );

        //$this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
    }
}