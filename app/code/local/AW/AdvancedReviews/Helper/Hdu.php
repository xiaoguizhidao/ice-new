<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AdvancedReviews
 * @version    2.3.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

/**
 * HDU Helper
 */
class AW_AdvancedReviews_Helper_Hdu extends Mage_Core_Helper_Abstract
{
    /**
     * Path to Help Desk Unlimate active flag
     *
     * @var String
     */
    const CONFIG_HDU_ACTIVE_PATH = 'modules/AW_Helpdeskultimate/active';

    /**
     * Path to Help Desk Unlimate integration enabled
     *
     * @var String
     */
    const CONFIG_HDU_ENABLED = 'advancedreviews/general/link_hdu';

    /**
     * Check HDU active
     *
     * @return boolean
     */
    public function isHDUActive()
    {
        return (Mage::getConfig()->getNode(self::CONFIG_HDU_ACTIVE_PATH) == 'true');
    }

    /**
     * Check HDU Enabled
     */
    public function isHDUEnabled()
    {
        return Mage::getStoreConfig(self::CONFIG_HDU_ENABLED);
    }

    /**
     * Add ticket for created Review
     *
     * @param Varien_Object $review Data of the Created Review
     *
     * @return AW_AdvancedReviews_Helper_Hdu
     */
    public function addTicket($review)
    {
        $subject = Mage::helper('advancedreviews')->__('Approve new review (%s)', $review->getTitle());
        $customer = $this->getCustomer($review->getCustomerId());
        $from = (Mage::helper('advancedreviews')->isUserLogged())
            ? $customer->getEmail()
            : Mage::registry(
                'advancedreviews_guest_email'
            );
        $message = Mage::app()->getLayout()
            ->createBlock('core/template')
            ->setDetail($review->getDetail())
            ->setReviewId($review->getReviewId())
            ->setTemplate('advancedreviews/hdu/ticket.phtml')
            ->toHtml();
        $source = 'advancedreviews';
        $proto = Mage::getModel('helpdeskultimate/proto');
        $proto->setSubject($subject)
            ->setContent($message)
            ->setFrom($from)
            ->setSource($source)
            ->save();
        $proto->convertToTicket();
        $proto->setStatus(AW_Helpdeskultimate_Model_Proto::STATUS_PROCESSED)->save();;
        return $this;
    }

    /**
     * Returns customer instance
     *
     * @param String|integer|null $customerId Id of customer who create review
     *
     * @return Varien_Object
     */
    public function getCustomer($customerId = null)
    {
        if ($customerId) {
            try {
                $customer = Mage::getModel('customer/customer')->load($customerId);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
            }
        } else {
            $customer = new Varien_Object(
                array(
                     'email' => Mage::getStoreConfig('trans_email/ident_general/email'),
                     'name'  => Mage::helper('advancedreviews')->__('Guest'),
                     'id'    => 0,
                )
            );
        }
        return $customer;
    }
}