<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Helper_Api
 */
class Oro_Friends_Helper_Api extends Oro_Friends_Helper_Data
{
    const XML_PATH_API_URI    = 'promo/oro_friends/api_uri';
    const XML_PATH_API_SECRET = 'promo/oro_friends/api_secret';
    const XML_PATH_ACCOUNT_ID = 'promo/oro_friends/account_id';

    protected $_model;

    /**
     * @return string
     */
    public function getAccountId()
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCOUNT_ID);
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URI);
    }

    /**
     * @return Oro_Friends_Model_Api
     */
    public function getApiModel()
    {
        if (is_null($this->_model)) {
            $this->_model = new Oro_Friends_Model_Api($this->getApiUrl());
        }

        return $this->_model;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return Mage::getStoreConfig(self::XML_PATH_API_SECRET);
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return string
     */
    public function getAuthToken(Mage_Customer_Model_Customer $customer)
    {
        $response = $this->getApiModel()->get('data/customer/auth_token', array(
            'uuid' => $this->getAccountId(),
            'email' => $customer->getData('email'),
        ));

        return $response->getData('data/auth_token');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function purchaseEvent(Mage_Sales_Model_Order $order)
    {
        $response = $this->getApiModel()->get('api/record.json', array(
            'uuid' => $this->getAccountId(),
            'email' => $order->getCustomerEmail(),
            'type' => 'purchase',
            'value' => $order->getSubtotal() + $order->getDiscountAmount(), // discount will be negative
            'event_id' => $order->getIncrementId(),
        ));

        return $response->getData('data/success');
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    public function sitevisitEvent(Mage_Customer_Model_Customer $customer)
    {
        $response = $this->getApiModel()->get('api/record.json', array(
            'uuid' => $this->getAccountId(),
            'email' => $customer->getData('email'),
            'type' => 'sitevisit',
        ));

        return $response->getData('data/success');
    }

    /**
     * @param string $email
     * @return bool
     */
    public function subscribeEvent($email)
    {
        $response = $this->getApiModel()->get('api/record.json', array(
            'uuid' => $this->getAccountId(),
            'email' => $email,
            'type' => 'signup',
        ));

        return $response->getData('data/success');
    }
}
