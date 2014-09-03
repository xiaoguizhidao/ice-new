<?php

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Buy_Synchronization_Tasks_Defaults_UpdateListingsProducts
    extends Ess_M2ePro_Model_Buy_Synchronization_Tasks
{
    const PERCENTS_START = 0;
    const PERCENTS_END = 100;
    const PERCENTS_INTERVAL = 100;

    const INTERVAL_COEFFICIENT_VALUE = 50000;

    const LOCK_ITEM_PREFIX = 'synchronization_buy_default_update_listings_products';

    //####################################

    public function process()
    {
        // PREPARE SYNCH
        //---------------------------
        $this->prepareSynch();
        //---------------------------

        // RUN SYNCH
        //---------------------------
        $this->execute();
        //---------------------------

        // CANCEL SYNCH
        //---------------------------
        $this->cancelSynch();
        //---------------------------
    }

    //####################################

    private function prepareSynch()
    {
        $this->_lockItem->activate();

        if (count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {
            $componentName = Ess_M2ePro_Helper_Component_Buy::TITLE.' ';
        } else {
            $componentName = '';
        }

        $this->_profiler->addEol();
        $this->_profiler->addTitle($componentName.'Update Listings Products');
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->addTimePoint(__CLASS__,'Total time');
        $this->_profiler->increaseLeftPadding(5);

        $this->_lockItem->setPercents(self::PERCENTS_START);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('Task "Update Listings Products" is started. Please wait...')
        );
    }

    private function cancelSynch()
    {
        $this->_lockItem->setPercents(self::PERCENTS_END);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('Task "Update Listings Products" is finished. Please wait...')
        );

        $this->_profiler->decreaseLeftPadding(5);
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->saveTimePoint(__CLASS__);

        $this->_lockItem->activate();
    }

    //####################################

    private function execute()
    {
        // Prepare last time
        $this->prepareLastTime();

        // Check locked last time
        if ($this->isLockedLastTime() &&
            $this->_initiator != Ess_M2ePro_Model_Synchronization_Run::INITIATOR_USER &&
            $this->_initiator != Ess_M2ePro_Model_Synchronization_Run::INITIATOR_DEVELOPER) {
            return;
        }

        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Buy')->getCollection('Account');
        $accountsTotalCount = $accountsCollection->getSize();

        $accountIteration = 1;
        $percentsForAccount = self::PERCENTS_INTERVAL;

        if ($accountsTotalCount > 0) {
            $percentsForAccount = self::PERCENTS_INTERVAL/(int)$accountsCollection->getSize();
        }

        foreach ($accountsCollection->getItems() as $accountObj) {

            /** @var $accountObj Ess_M2ePro_Model_Account */

            if (!$this->isLockedAccount($accountObj->getId())) {

                /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
                $collection = Mage::getModel('M2ePro/Listing')->getCollection();
                $collection->addFieldToFilter('component_mode',Ess_M2ePro_Helper_Component_Buy::NICK);
                $collection->addFieldToFilter('account_id',(int)$accountObj->getId());

                if ($collection->getSize()) {
                    $this->updateAccount($accountObj);
                }
            }

            $this->_lockItem->setPercents(self::PERCENTS_START + $percentsForAccount*$accountIteration);
            $this->_lockItem->activate();
            $accountIteration++;
        }

        $this->setCheckLastTime(Mage::helper('M2ePro')->getCurrentGmtDate(true));
    }

    private function updateAccount(Ess_M2ePro_Model_Account $accountObj)
    {
        $this->_profiler->addTitle(
            'Starting account "'.$accountObj->getTitle().'"'
        );
        $this->_profiler->addTimePoint(__METHOD__.'send'.$accountObj->getId(),'Get inventory from Buy');
        $status = 'Task "Update Listings Products" for Rakuten.com account: "%s" is started. Please wait...';
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__($status, $accountObj->getTitle())
        );

        // Get all changes on Buy for account
        //---------------------------
        $dispatcherObject = Mage::getModel('M2ePro/Connector_Buy_Dispatcher');
        $dispatcherObject->processConnector('defaults', 'updateListingsProducts' ,'requester',
                                            array(), $accountObj,
                                            'Ess_M2ePro_Model_Buy_Synchronization_Tasks');
        //---------------------------

        $this->_profiler->saveTimePoint(__METHOD__.'send'.$accountObj->getId());
        $this->_profiler->addEol();
    }

    //####################################

    private function prepareLastTime()
    {
        $lastTime = $this->getCheckLastTime();
        if (empty($lastTime)) {
            $lastTime = new DateTime('now', new DateTimeZone('UTC'));
            $lastTime->modify("-1 year");
            $this->setCheckLastTime($lastTime);
        }
    }

    private function isLockedLastTime()
    {
        $lastTime = strtotime($this->getCheckLastTime());

        $tempGroup = '/buy/defaults/update_listings_products/';
        $interval = (int)Mage::helper('M2ePro/Module')->getSynchronizationConfig()->getGroupValue($tempGroup,'interval');

        $totalItems = (int)Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Product')->getSize();
        $totalItems += (int)Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Other')->getSize();
        $intervalCoefficient = ($totalItems > 0) ? (int)ceil($totalItems/self::INTERVAL_COEFFICIENT_VALUE) : 1;

        if ($lastTime + ($interval*$intervalCoefficient) > Mage::helper('M2ePro')->getCurrentGmtDate(true)) {
            return true;
        }

        return false;
    }

    private function getCheckLastTime()
    {
        $tempGroup = '/buy/defaults/update_listings_products/';
        return Mage::helper('M2ePro/Module')->getSynchronizationConfig()->getGroupValue($tempGroup,'last_time');
    }

    private function setCheckLastTime($time)
    {
        if ($time instanceof DateTime) {
            $time = (int)$time->format('U');
        }
        if (is_int($time)) {
            $oldTimezone = date_default_timezone_get();
            date_default_timezone_set('UTC');
            $time = strftime('%Y-%m-%d %H:%M:%S', $time);
            date_default_timezone_set($oldTimezone);
        }
        $tempGroup = '/buy/defaults/update_listings_products/';
        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue($tempGroup,'last_time',$time);
    }

    //------------------------------------

    private function isLockedAccount($accountId)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');
        $lockItem->setNick(self::LOCK_ITEM_PREFIX.'_'.$accountId);

        $tempGroup = '/buy/defaults/update_listings_products/';
        $maxDeactivateTime = (int)Mage::helper('M2ePro/Module')->getSynchronizationConfig()
                                    ->getGroupValue($tempGroup,'max_deactivate_time');
        $lockItem->setMaxDeactivateTime($maxDeactivateTime);

        if ($lockItem->isExist()) {
            return true;
        }

        return false;
    }

    //####################################
}