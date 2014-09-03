<?php
/**
 * @category   Oro
 * @package    Oro_Linkshare
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Linkshare_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function dispatchSiteIdParam(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Controller_Front_Action $controller */
        $controller = $observer->getEvent()->getData('controller_action');
        if ($value = $controller->getRequest()->getParam('siteID')) {
            $name = trim((string) Mage::helper('linkshare')->getConfig('cookie_name'));
            Mage::getModel('core/cookie')->set($name, $value);
            if ($redirectUrl = $controller->getRequest()->getParam('url')) {
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect($redirectUrl);
                $controller->postDispatch();
            }
        }
    }
}
