<?php

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
*/

class Ess_M2ePro_Model_Play_Synchronization_Tasks_OtherListings extends Ess_M2ePro_Model_Play_Synchronization_Tasks
{
    const PERCENTS_START = 0;
    const PERCENTS_END = 100;
    const PERCENTS_INTERVAL = 100;

    const INTERVAL_COEFFICIENT_VALUE = 10000;

    const LOCK_ITEM_PREFIX = 'synchronization_play_other_listings';

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
        $this->_logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_OTHER_LISTINGS);

        if (count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {
            $componentName = Ess_M2ePro_Helper_Component_Play::TITLE.' ';
        } else {
            $componentName = '';
        }

        $this->_profiler->addEol();
        $this->_profiler->addTitle($componentName.'3rd Party Listings Synchronization');
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->addTimePoint(__CLASS__, 'Total time');
        $this->_profiler->increaseLeftPadding(5);

        $this->_lockItem->setTitle(Mage::helper('M2ePro')->__($componentName.'3rd Party Listings Synchronization'));
        $this->_lockItem->setPercents(self::PERCENTS_START);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('Task "3rd Party Listings Synchronization" is started. Please wait...')
        );
    }

    private function cancelSynch()
    {
        $this->_lockItem->setPercents(self::PERCENTS_END);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('Task "3rd Party Listings Synchronization" is finished. Please wait...')
        );

        $this->_profiler->decreaseLeftPadding(5);
        $this->_profiler->addEol();
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->saveTimePoint(__CLASS__);

        $this->_logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_UNKNOWN);
        $this->_lockItem->activate();
    }

    //####################################

    private function execute()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Get and process items from Play');

        // Prepare last time
        $this->prepareLastTime();

        // Check locked last time
        if ($this->isLockedLastTime() &&
            $this->_initiator != Ess_M2ePro_Model_Synchronization_Run::INITIATOR_USER &&
            $this->_initiator != Ess_M2ePro_Model_Synchronization_Run::INITIATOR_DEVELOPER) {
            return;
        }

        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Play')->getCollection('Account');
        $accountsCollection->addFieldToFilter('other_listings_synchronization',
                                              Ess_M2ePro_Model_Play_Account::OTHER_LISTINGS_SYNCHRONIZATION_YES);

        $percentsForAccount = self::PERCENTS_INTERVAL;
        $accountsTotalCount = (int)$accountsCollection->getSize();

        if ($accountsTotalCount > 0) {
            $percentsForAccount = $percentsForAccount/$accountsTotalCount;
        }

        $accountIteration = 1;
        foreach ($accountsCollection->getItems() as $accountObj) {

            /** @var $accountObj Ess_M2ePro_Model_Account */

            if (!$this->isLockedAccount($accountObj->getId())) {
                $this->updateAccount($accountObj);
            }

            $this->_lockItem->setPercents(self::PERCENTS_START + $percentsForAccount*$accountIteration);
            $this->_lockItem->activate();
            $accountIteration++;
        }

        $this->setCheckLastTime(Mage::helper('M2ePro')->getCurrentGmtDate(true));
        $this->_profiler->saveTimePoint(__METHOD__);
    }

    private function updateAccount(Ess_M2ePro_Model_Account $accountObj)
    {
        $this->_profiler->addTitle(
            'Starting account "'.$accountObj->getTitle().'"'
        );
        $this->_profiler->addTimePoint(__METHOD__.'send'.$accountObj->getId(),'Get inventory from Play');

        $tempString = Mage::helper('M2ePro')->__('Task "3rd Party Listings Synchronization" for Play.com account: ');
        $tempString .= Mage::helper('M2ePro')->__('"%s" is started. Please wait...',
                                                  $accountObj->getTitle());
        $this->_lockItem->setStatus($tempString);

        // Get all changes on Play for account
        //---------------------------
        $dispatcherObject = Mage::getModel('M2ePro/Connector_Play_Dispatcher');
        $dispatcherObject->processConnector('tasks', 'otherListings' ,'requester',
                                            array(), $accountObj,
                                            'Ess_M2ePro_Model_Play_Synchronization');
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

        $tempGroup = '/play/other_listings/';
        $interval = (int)Mage::helper('M2ePro/Module')->getSynchronizationConfig()->getGroupValue($tempGroup,'interval');

        $totalItems = (int)Mage::helper('M2ePro/Component_Play')->getCollection('Listing_Product')->getSize();
        $totalItems += (int)Mage::helper('M2ePro/Component_Play')->getCollection('Listing_Other')->getSize();
        $intervalCoefficient = ($totalItems > 0) ? (int)ceil($totalItems/self::INTERVAL_COEFFICIENT_VALUE) : 1;

        if ($lastTime + ($interval*$intervalCoefficient) > Mage::helper('M2ePro')->getCurrentGmtDate(true)) {
            return true;
        }

        return false;
    }

    private function getCheckLastTime()
    {
        $tempGroup = '/play/other_listings/';
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
        $tempGroup = '/play/other_listings/';
        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue($tempGroup,'last_time',$time);
    }

    //------------------------------------

    private function isLockedAccount($accountId)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');
        $lockItem->setNick(self::LOCK_ITEM_PREFIX.'_'.$accountId);

        $tempGroup = '/play/other_listings/';
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