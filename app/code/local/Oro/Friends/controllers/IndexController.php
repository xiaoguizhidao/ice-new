<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * LoyaltyPlus Customer Account Controller
 *
 * Class Oro_Friends_Adminhtml_Customer_RewardsController
 */
class Oro_Friends_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check is customer logged in
     *
     * @return Mage_Core_Controller_Front_Action|void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

//    /**
//     * Go ot program page
//     */
//    public function indexAction()
//    {
//        $this->_redirect('*/*/program');
//    }

    /**
     * LoyaltyPlus program info page
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->renderLayout();
    }
}
