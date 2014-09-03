<?php
/**
 * GoMage.com
 *
 * GoMage Feed Pro
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2013 GoMage.com (http://www.gomage.com)
 * @author       GoMage.com
 * @license      http://www.gomage.com/licensing  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 3.3
 * @since        Class available since Release 1.0
 */

class GoMage_Feed_Adminhtml_FeedController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Generate feed
     */
    public function generateAction()
    {
        /** @var GoMage_Feed_Model_Item $feed */
        $feed = Mage::getModel('gomage_feed/item')->load($this->getRequest()->getParam('feed_id'));
        @ignore_user_abort(true);
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Connection', 'close', true)
            ->setHeader('Content-Length', '0', true);
        $this->getResponse()->clearBody();
        $this->getResponse()->sendResponse();
        /** @var GoMage_Feed_Model_Generator $generateInfo */
        $generateInfo = Mage::helper('gomage_feed/generator')->getGenerateInfo($feed->getId());
        if (!$generateInfo->inProcess()) {
            $feed->generateFeed();
        }
    }
}
