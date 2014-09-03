<?php
/**
 * @category   Oro
 * @package    Oro_Migration
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Admin controller
 */
class Oro_Migration_Adminhtml_MigrationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init action
     *
     * @return $this
     */
    protected function _initLayout()
    {
        $this->loadLayout();
        $this->_setActiveMenu('migration');

        return $this;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Phase 1 action
     */
    public function incrementAction()
    {
        $table = $this->getRequest()->getPost('table_name');

        if ($table) {
            try {
                $processor = Mage::getResourceModel('oro_migration/increment');
                $processor->increment($this->getRequest()->getPost());

                $this->getResponse()->setBody(Zend_Json::encode(array('processed' => true)));
            } catch (Exception $e) {
                $this->getResponse()->setBody(Zend_Json::encode(array('error' => true, 'message' => $e->getMessage())));
            }
        }
    }

    public function migrateAction()
    {
        $table = $this->getRequest()->getPost('table_name');

        if ($table) {
            try {
                $processor = Mage::getResourceModel('oro_migration/increment');

                if ($this->getRequest()->getParam('action') == 'update') {
                    $result = $processor->update($this->getRequest()->getPost());
                } else {
                    $result = $processor->migrate($this->getRequest()->getPost());
                }

                $this->getResponse()->setBody(Zend_Json::encode(array('processed' => true, 'result' => $result)));
            } catch (Exception $e) {
                $this->getResponse()->setBody(Zend_Json::encode(array('error' => true, 'message' => $e->getMessage())));
            }
        }
    }

    public function migrateCouponsAction()
    {
        try {
            $processor = Mage::getResourceModel('oro_migration/increment');
            $result = $processor->syncCoupons($this->getRequest()->getPost());

            $this->getResponse()->setBody(Zend_Json::encode(array('processed' => true, 'message' => $result)));
        } catch (Exception $e) {
            $this->getResponse()->setBody(Zend_Json::encode(array('error' => true, 'message' => $e->getMessage())));
        }
    }

    public function flushAction()
    {
        if ($this->getRequest()->getParam('increments')) {
            Mage::getResourceModel('oro_migration/increment')->flush();
        }

        $this->_redirect('*/*');
    }

    public function incrementIDAction()
    {
        $value = $this->getRequest()->getPost('increment_value');

        if ($value) {
            try {
                $processor = Mage::getResourceModel('oro_migration/increment');
                $result = $processor->incrementIDs($value);

                $this->getResponse()->setBody(Zend_Json::encode(array('processed' => true, 'result' => $result)));
            } catch (Exception $e) {
                $this->getResponse()->setBody(Zend_Json::encode(array('error' => true, 'message' => $e->getMessage())));
            }
        }
    }

}
