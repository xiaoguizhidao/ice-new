<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Adminhtml_Oro_Friends_Promo_QuoteController
 */
class Oro_Friends_Adminhtml_Oro_Friends_Promo_QuoteController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Import Coupons
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['datafile']) && is_file($_FILES['datafile']['tmp_name'])) {
                try {
                    $errors = array();
                    $imported = Mage::getSingleton('oro_friends/coupon_import')->import(
                        $_FILES['datafile']['tmp_name'],
                        $this->getRequest()->getPost('rule_id'),
                        $this->getRequest()->getPost('delete_exists', false),
                        $errors
                    );
                    $this->_getSession()->addSuccess($this->__('Total Imported: %d.', $imported));
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }
                    }
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirectReferer();
    }
}
