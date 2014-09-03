<?php
/**
 * ChannelAdvisor Observer Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Observer extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init("bridgechanneladvisor/observer");
    }

    public function getObserverCollection()
    {
        return Mage::getModel('bridgechanneladvisor/observer')->getCollection();
    }

    public function  sendEmailError()
    {
        $EmailEnable = Mage::getStoreConfig('bridgechanneladvisor/importemail/notifyActive');
        if($EmailEnable){
            $errorsProductsImport = array();
            $errorsProductsQtyUpdate = array();
            $errorsProductsUpdate = array();
            $errorsOrdersImport = array();
            $errorsProductsExport = array();
            $errorsOrdersExport = array();
            $errorsProductsImportRA = array();
            $errorsProductsUpdateRA = array();
            $errorsProductsQtyExport = array();
            $errorsProductsUpdateQtyRA = array();
            $allData = array();

            $errors = Mage::getModel('bridgechanneladvisor/errornotify')->getCollection();

            foreach($errors as $error){
                if($error->getProcessId() == 1){
                    $errorsProductsImport[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 2){
                    $errorsProductsQtyUpdate[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 3){
                    $errorsProductsUpdate[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 4){
                    $errorsOrdersImport[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 5){
                    $errorsProductsExport[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 6){
                    $errorsOrdersExport[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 7){
                    $errorsProductsImportRA[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 8){
                    $errorsProductsUpdateRA[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 9){
                    $errorsProductsQtyExport[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
                if($error->getProcessId() == 10){
                    $errorsProductsUpdateQtyRA[] = $error->getRecordingTime().' - '.$error->getMessage();
                }
            }

            if(count($errorsProductsImport) > 0){
                array_unshift($errorsProductsImport, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '1')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsImport;
            }
            if(count($errorsProductsQtyUpdate) > 0){
                array_unshift($errorsProductsQtyUpdate, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '2')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsQtyUpdate;
            }
            if(count($errorsProductsUpdate) > 0){
                array_unshift($errorsProductsUpdate, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '3')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsUpdate;
            }
            if(count($errorsOrdersImport) > 0){
                array_unshift($errorsOrdersImport, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '4')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsOrdersImport;
            }
            if(count($errorsProductsExport) > 0){
                array_unshift($errorsProductsExport, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '5')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsExport;
            }
            if(count($errorsOrdersExport) > 0){
                array_unshift($errorsOrdersExport, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '6')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsOrdersExport;
            }
            if(count($errorsProductsImportRA) > 0){
                array_unshift($errorsProductsImportRA, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '7')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsImportRA;
            }
            if(count($errorsProductsUpdateRA) > 0){
                array_unshift($errorsProductsUpdateRA, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '8')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsUpdateRA;
            }
            if(count($errorsProductsQtyExport) > 0){
                array_unshift($errorsProductsQtyExport, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '9')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsQtyExport;
            }
            if(count($errorsProductsUpdateQtyRA) > 0){
                array_unshift($errorsProductsUpdateQtyRA, Mage::getModel('bridgechanneladvisor/errorname')->getCollection()->addFieldToFilter('process_id', '10')->getFirstItem()->getProcessTitle());
                $allData = $allData + $errorsProductsUpdateQtyRA;
            }

            /** @var QS_BridgeChannelAdvisor_Helper_Data  $helper */
            $helper = Mage::helper('bridgechanneladvisor');
            $helper->sendMail($allData);

        }
        return;
    }

    public function adminSessionUserLoginSuccess() {
        Mage::helper('bridgechanneladvisor')->checkRequirements();
    }

    /**
     * Check cron running interval.
     */
    public function checkCronSettings(){
        $dynamicConfigsModel = Mage::getModel('bridgechanneladvisor/dynamicconfigs');
        $lastCronRunningTimeRecord = $dynamicConfigsModel->load('last_cron_running','config_key');
        /** @var QS_BridgeChannelAdvisor_Helper_Data  $helper */
        $helper = Mage::helper('bridgechanneladvisor');

        $helper->_setCurrentValue('cron_check',1);//Cron ok

        $this->checkMaxExecutionTime();
        $this->checkMemoryLimit();

        if((!$helper->_getCurrentValue('email_send') && ($helper->_getCurrentValue('max_execution_time') != ini_get('max_execution_time')))
        ||(!$helper->_getCurrentValue('email_send') && ($helper->_getCurrentValue('memory_limit') != substr(ini_get('memory_limit'), 0, -1)))) {
            $helper->sendEmail();
        }

    }

    public  function checkMaxExecutionTime() {
        /** @var QS_BridgeChannelAdvisor_Helper_Data  $helper */
        $helper = Mage::helper('bridgechanneladvisor');
            if($helper->_getCurrentValue('max_execution_time') != ini_get('max_execution_time')) {
                $helper->_setCurrentValue('email_send',0);
            }

        $helper->_setCurrentValue('max_execution_time',(int)ini_get('max_execution_time'));
    }

    public  function checkMemoryLimit() {
        /** @var QS_BridgeChannelAdvisor_Helper_Data  $helper */
        $helper = Mage::helper('bridgechanneladvisor');
        if($helper->_getCurrentValue('memory_limit') != substr(ini_get('memory_limit'), 0, -1)) {
            $helper->_setCurrentValue('email_send',0);
        }

        $helper->_setCurrentValue('memory_limit',(int)substr(ini_get('memory_limit'), 0, -1));
    }
}
