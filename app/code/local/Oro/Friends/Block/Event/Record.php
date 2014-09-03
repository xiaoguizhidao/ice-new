<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Friends_Block_Event_Record
    extends Oro_Friends_Block_Customer_Abstract
{
    /**
     * @return string
     */
    public function getRecordGifUrl()
    {
        return Mage::helper('oro_friends/api')->getApiUrl();
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return Mage::helper('oro_friends/api')->getAccountId();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }

    /**
     * @return string
     */
    public function getEventUrl()
    {
        return "http://loyalty.500friends.com/api/record.gif?uuid=". $this->getAccountId()
        . "&email=" . $this->getCustomerEmail()
        . "&type=" . $this->getEventType()
        . "&event_id=" . $this->getEventId()
        . "&value=" . $this->getValue()
        . "&detail=" . $this->getDetail();
    }
}
