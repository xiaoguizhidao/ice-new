<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Block_Customer_Abstract
 */
abstract class Oro_Friends_Block_Customer_Abstract
    extends Mage_Core_Block_Template
{
    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return Mage::helper('oro_friends/api')->getAccountId();
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        $token =  Mage::getSingleton('customer/session')->getData('oro_friends_token');
        
        if (!$token) {
            $token = Mage::helper('oro_friends/api')->getAuthToken($this->getCustomer());
            if ($token) {
                Mage::getSingleton('customer/session')->setData('oro_friends_token', $token);
            }
        }

        return $token;
    }
}
