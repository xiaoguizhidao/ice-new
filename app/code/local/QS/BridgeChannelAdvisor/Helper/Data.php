<?php
/**
 * ChannelAdvisor Content Data Helper
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR = 2;
    const SEVERITY_MINOR = 3;
    const SEVERITY_NOTICE = 4;

    /**
     * Get ChannelAdvisor Attributes by ChannelAdvisor Category Id
     *
     * @return array
     */
    public function getAttributesByCategoryId($categoryId)
    {
        $result[] = array('label' => $this->__('Custom attribute, no mapping'));
        $collection = Mage::getModel('bridgechanneladvisor/relation')->getCollection()->addFieldToFilter('category_id', $categoryId);

        foreach ($collection as $item) {
            $attribute = Mage::getModel('bridgechanneladvisor/attribute')->getCollection()->addFieldToFilter('attribute_id', $item->getAttributeId())->getFirstItem();
            $result[$item->getAttributeId()] = array('label' => ucwords(htmlspecialchars($attribute->getAttributeName(), ENT_QUOTES)));
        }

        return $result;
    }

    public function _getNotifier()
    {
        return $this;
    }

    /**
     * Add new message
     *
     * @param int $severity
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function add($severity, $title = null, $description = null, $url = '', $isInternal = true)
    {
        if (!$this->getSeverities($severity)) {
            Mage::throwException($this->__('Wrong message type'));
        }
        if (is_array($description)) {
            $description = '<ul><li>' . implode('</li><li>', $description) . '</li></ul>';
        }
        $date = date('Y-m-d H:i:s');
        Mage::getModel('adminnotification/inbox')->parse(array(array(
            'severity' => $severity,
            'date_added' => $date,
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'internal' => $isInternal
        )));
        return $this;
    }

    /**
     * Add major severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function addMajor($title = null, $description = null, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MAJOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add notice
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function addNotice($title = null, $description = null, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_NOTICE, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Retrieve Severity collection array
     *
     * @return array|string
     */
    public function getSeverities($severity = null)
    {
        $severities = array(
            self::SEVERITY_CRITICAL => Mage::helper('adminnotification')->__('critical'),
            self::SEVERITY_MAJOR => Mage::helper('adminnotification')->__('major'),
            self::SEVERITY_MINOR => Mage::helper('adminnotification')->__('minor'),
            self::SEVERITY_NOTICE => Mage::helper('adminnotification')->__('notice'),
        );

        if (!is_null($severity)) {
            if (isset($severities[$severity])) {
                return $severities[$severity];
            }
            return null;
        }

        return $severities;
    }

    /**
     * Get ChannelAdvisor Flag
     *
     * @param $value
     * @return string
     */
    public function getCaFlag($value)
    {
        switch ($value) {

            case 1:
                $val = 'ExclamationPoint';
                break;
            case 2:
                $val = 'QuestionMark';
                break;
            case 3:
                $val = 'NotAvailable';
                break;
            case 4:
                $val = 'Price';
                break;
            case 5:
                $val = 'BlueFlag';
                break;
            case 6:
                $val = 'GreenFlag';
                break;
            case 7:
                $val = 'RedFlag';
                break;
            case 8:
                $val = 'YellowFlag';
                break;
            case 9:
                $val = 'ItemCopied';
                break;

            default:
                $val = 'NoFlag';
                break;
        }

        return $val;
    }

    public function _getRequirements($config_value)
    {
        $settings = Mage::getModel('bridgechanneladvisor/dynamicconfigs')->getCollection();
        try{
            if(count($settings) > 0){
                return $settings->getItemByColumnValue('config_key', $config_value)->getConfigValue();
            }else{
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public function _setCurrentValue($config_value, $current_config_value)
    {
        $dynamicConfigsModel = Mage::getModel('bridgechanneladvisor/dynamicconfigs');
        try {
            $lastCronRunningTimeRecord = $dynamicConfigsModel->load($config_value, 'config_key');
            $lastCronRunningTimeRecord->setCurrentValue($current_config_value)->save();
        } catch (Exception $e) {
            mage::log($e->getMessage());
        }
    }

    public function  _getCurrentValue($config_value)
    {
        $settings = Mage::getModel('bridgechanneladvisor/dynamicconfigs')->getCollection();
        return $settings->getItemByColumnValue('config_key', $config_value)->getCurrentValue();
    }

    public function checkRequirements()
    {
        $max_execution_time = $this->_getRequirements('max_execution_time');
        $memory_limit = $this->_getRequirements('memory_limit');
        $max_allowed_packet = $this->_getRequirements('max_allowed_packet');


        $this->_checkcron();
        $this->_soapcheck();
        $this->_checkioncube();

        if (($this->checkMaxAllowedPacket($max_allowed_packet))
            || ($this->checkSOAP())
            || ($this->checkIONCUBE())

            || ((($this->_getCurrentValue('max_execution_time') < $max_execution_time)&&($this->_getCurrentValue('max_execution_time')!= 0))&&(!is_null($this->_getCurrentValue('max_execution_time'))))

            || ((!is_null($this->_getCurrentValue('memory_limit')))&&($this->_getCurrentValue('memory_limit') < $memory_limit))

            || ($this->checkCredentials())
            || ($this->checkCronMessage())
        ) {
            if (!$this->_getCurrentValue('email_send')) {
                $this->sendEmail();
            }
            $this->_setCurrentValue('status_check', 0);
            return false;
        }
        $this->_setCurrentValue('status_check', 1);
        return true;
    }

    public function showErrorMessages($max_execution_time, $memory_limit, $max_allowed_packet)
    {
        $messages = array();
        $resp = '';

        if (($this->_getCurrentValue('max_execution_time') < $max_execution_time)&&($this->_getCurrentValue('max_execution_time')!= 0)&&(!is_null($this->_getCurrentValue('max_execution_time')))) {
            //$messages[] = $this->__('max_execution_time should be ' . $max_execution_time . ' or more. Now max_execution_time = ' . $this->_getCurrentValue('max_execution_time'));
            $messages[] = $this->__("Max_Execution_Time Error! – Server max_excecution time should be set to ".$max_execution_time." or more - Please contact your hosting company to correct this. The ChannelAdvisor Bridge will not work without a Max Execution Setting time of ".$max_execution_time." or higher");
        }

        if (($this->_getCurrentValue('memory_limit') < $memory_limit)&&(!is_null($this->_getCurrentValue('memory_limit')))) {
            //$messages[] = $this->__('memory_limit should be ' . $memory_limit . 'M or more. Now memory_limit = ' . $this->_getCurrentValue('memory_limit') . 'M');
            $messages[] = $this->__("Memory Limit Error! - Memory_limit should be ".$memory_limit."M or higher.  Your current Memory_limit = ".$this->_getCurrentValue('memory_limit')."M – Please contact your hosting company to correct this. The ChannelAdvisor Bridge will not work without min Memory requirements. ");
        }

        $messages[] = $this->checkMaxAllowedPacket($max_allowed_packet);
        $messages[] = $this->checkSOAP();
        $messages[] = $this->checkIONCUBE();
        $messages[] = $this->checkCredentials();
        $messages[] = $this->checkCronMessage();
        $license = Mage::helper('core')->decrypt(Mage::getStoreConfig('bridgechanneladvisor/license/ca_license'));
        if($license != ''){
            $resp = Mage::getModel('bridgechanneladvisor/camage')->cl($license);
            $messages[] = $resp['message'];
        }

        return $messages;
    }

    public function checkCronMessage()
    {
        $cron_status = $this->_getCurrentValue('cron_check');
        if((!$cron_status)){
            return  $this->__('Cron Job Error ! – You do not have the cron jobs setup correctly. If this message does not disappear after 5-10 minutes, please log out of the admin panel and log in again. If this message appears again you have problems with cron setting. The ChannelAdvisor Bridge Extension cron needs to run every 5 min. The ChannelAdvisor Bridge will not work correctly without this in place. ');
        }
        return false;
    }

    protected function _checkcron()
    {
        $jobCode = 'bridgechanneladvisor_check_cron_settings';
        /** @var $schedule Mage_Cron_Model_Mysql4_Schedule_Collection */
        $schedule = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code',$jobCode)
            ->addFieldToFilter('status','success')
            ->setOrder('schedule_id');

        $time_last_success_run = Mage::getModel('core/date')->gmtTimestamp($schedule->getFirstItem()->getData('finished_at'));
        $current_time = Mage::getModel('core/date')->gmtTimestamp(time());
        $interval = 600; // 10 minutes

        if((($current_time - $time_last_success_run) > $interval)||(!$schedule->getFirstItem()->getId())) {
            $this->_setCurrentValue('cron_check', 0);
        }

    }

    public function checkMaxAllowedPacket($max_allowed_packet)
    {
        /** $max_allowed_packet in bytes  */
        $max_allowed_packet_b = $max_allowed_packet * 1024 * 1024;
        /** $max_allowed_packet in Mbytes  */
        $max_allowed_packet_mb = $this->_checkMaxAllowedPacket() / (1024 * 1024);

        if ($this->_getCurrentValue('max_allowed_packet') != $max_allowed_packet_mb) {
            $this->_setCurrentValue('email_send', 0);
        }

        $this->_setCurrentValue('max_allowed_packet', $max_allowed_packet_mb);
        if ($this->_checkMaxAllowedPacket() < $max_allowed_packet_b) {
            //$message = $this->__('max_allowed_packet should be ' . $max_allowed_packet . 'M or more. Now max_allowed_packet = ' . $max_allowed_packet_mb . 'M');
            $message = $this->__("Max Allowed Packet Error! Max Allowed Packet should be ".$max_allowed_packet."M  Please contact your hosting company and confirm that our minimum server requirements are put in place in order for extension to work correctly.");
            return $message;
        } else {
            return false;
        }
    }

    public function  checkSOAP()
    {
        if (!extension_loaded('soap')) {
            $message = $this->__('Soap Library Error! – You do not have the correct Soap Library on your server. We are using a SOAP library to make API calls - <a href="http://php.net/manual/en/book.soap.php">http://php.net/manual/en/book.soap.php</a> Please contact your hosting company and confirm that you server has the correct SOAP library installed. The ChannelAdvisor Bridge will not work without correct Soap library.');
            $this->_setCurrentValue('soap', 0);
            return $message;
        } else {
            $this->_setCurrentValue('soap', 1);
            return false;
        }
    }

    protected function _soapcheck() {
        if((extension_loaded('soap'))&&($this->_getCurrentValue('soap') == 0)
        ||(!(extension_loaded('soap')) && ($this->_getCurrentValue('soap') == 1))){
            $this->_setCurrentValue('email_send',0);
        }
    }

    protected function _checkioncube(){
        $ioncube_loader_version = $this->_ioncube_loader_version_array();
        if(((extension_loaded('ionCube Loader'))&&($this->_getCurrentValue('ioncube') == 0))
            ||(((!extension_loaded('ionCube Loader'))&&($this->_getCurrentValue('ioncube') == 1)))
            ||(($ioncube_loader_version['major'] < 4
                            || ($ioncube_loader_version['major'] == 4 && $ioncube_loader_version['minor'] < 4)
                            || ($ioncube_loader_version['major'] == 4 && $ioncube_loader_version['minor'] == 4 && ($ioncube_loader_version['revision'] < 1) && $ioncube_loader_version['revision'] != '')
                        )&&($this->_getCurrentValue('ioncube') == 1))){
            $this->_setCurrentValue('email_send',0);
        }
    }

    public function checkIONCUBE()
    {
        $instructions = '';
        if (extension_loaded('ionCube Loader')) {
            $ioncube_loader_version = $this->_ioncube_loader_version_array();

            if($ioncube_loader_version){
                if ($ioncube_loader_version['major'] < 4
                    || ($ioncube_loader_version['major'] == 4 && $ioncube_loader_version['minor'] < 4)
                    || ($ioncube_loader_version['major'] == 4 && $ioncube_loader_version['minor'] == 4 && ($ioncube_loader_version['revision'] < 1) && $ioncube_loader_version['revision'] != '')
                ) {
                    $instructions = $this->__("Ioncube Loader  Error! – You do not have the correct version in place. Please contact your hosting company and confirm that you have an Ioncube loader updated to version 4.4.1. The ChannelAdvisor Bridge will not work without an Ioncube loader version 4.4.1 or higher");
                    $this->_setCurrentValue('ioncube', 0);
                    return $instructions;
                }
            }else{
                $instructions = $this->__("Ioncube Not Installed Error! – You do not have an Ioncube loader installed on your server. Please contact your hosting company and confirm that you have an Ioncube loader updated to version 4.4.1. The ChannelAdvisor Bridge will not work without an IonCube installed ");
                $this->_setCurrentValue('ioncube', 0);
                return $instructions;
            }

        } else {
            $instructions = $this->__("Ioncube Not Installed Error! – You do not have an Ioncube loader installed on your server. Please contact your hosting company and confirm that you have an Ioncube loader updated to version 4.4.1. The ChannelAdvisor Bridge will not work without an IonCube installed ");
            $this->_setCurrentValue('ioncube', 0);
            return $instructions;
        }

        $this->_setCurrentValue('ioncube', 1);
        return false;
    }

    protected function _ioncube_loader_version_array()
    {
        if (function_exists('ioncube_loader_iversion')) {
            $ioncube_loader_iversion = ioncube_loader_iversion();
            $ioncube_loader_version_major = (int)substr($ioncube_loader_iversion, 0, 1);
            $ioncube_loader_version_minor = (int)substr($ioncube_loader_iversion, 1, 2);
            $ioncube_loader_version_revision = (int)substr($ioncube_loader_iversion, 3, 2);
            $ioncube_loader_version = "$ioncube_loader_version_major.$ioncube_loader_version_minor.$ioncube_loader_version_revision";

            return array('version' => $ioncube_loader_version, 'major' => $ioncube_loader_version_major, 'minor' => $ioncube_loader_version_minor, 'revision' => $ioncube_loader_version_revision);

        }

        if(function_exists('ioncube_loader_version')){
            $ioncube_loader_version = ioncube_loader_version();
            $ioncube_loader_version_major = (int)substr($ioncube_loader_version, 0, 1);
            $ioncube_loader_version_minor = (int)substr($ioncube_loader_version, 2, 1);
            $ioncube_loader_version_revision = '';

            return array('version' => $ioncube_loader_version, 'major' => $ioncube_loader_version_major, 'minor' => $ioncube_loader_version_minor, 'revision' => $ioncube_loader_version_revision);
        }

        return false;

    }

    protected function _checkMaxAllowedPacket()
    {
        $w = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $w->query('SHOW VARIABLES LIKE "max_allowed_packet"');
        $row = $result->fetch(PDO::FETCH_ASSOC);

        return $row['Value'];
    }

    public function sendEmail()
    {
        $message = $this->showErrorMessages($this->_getRequirements('max_execution_time'), $this->_getRequirements('memory_limit'), $this->_getRequirements('max_allowed_packet'));

        if($this->sendMail($message)){
            $this->_setCurrentValue('email_send', 1);
        }
    }

    public function checkCredentials()
    {
        $accountID = Mage::helper('core')->decrypt(Mage::getStoreConfig('bridgechanneladvisor/bridgechanneladvisor/account_id', Mage::app()->getStore()));
        $developerKey = Mage::helper('core')->decrypt(Mage::getStoreConfig('bridgechanneladvisor/bridgechanneladvisor/dev_key', Mage::app()->getStore()));
        $password = Mage::helper('core')->decrypt(Mage::getStoreConfig('bridgechanneladvisor/bridgechanneladvisor/dev_password', Mage::app()->getStore()));
        //$localID = Mage::helper('core')->decrypt(Mage::getStoreConfig('bridgechanneladvisor/bridgechanneladvisor/local_id', Mage::app()->getStore()));

        if ((!$accountID) || (!$developerKey) || (!$password)) {
            return 'Developer Key, ChannelAdvisor Account ID or Password Error ! - Your developer or Key Password are not installed correctly. Please review user guide and make sure all steps were followed. The ChannelAdvisor Bridge will not work without this in place.';
        } else {
            return false;
        }
    }

    public function sendMail($allData) {
        $emails = Mage::getStoreConfig('bridgechanneladvisor/importemail/caimportemail');
        $arrEmails = explode(';', $emails);
        $arrEmails[]= Mage::getStoreConfig('trans_email/ident_general/email');
        $arrEmails[]= 'info@ecomitize.com';
        if(count($arrEmails) > 0 && $arrEmails != ''){
            $items = '';
            foreach($allData as $line){
                $items .= $line. "\n";
            }
            try{
                $mailTemplate = Mage::getModel('core/email_template');
                $template = Mage::getStoreConfig('bridgechanneladvisor/importemail/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                foreach ($arrEmails as $id=>$recipient){
                    if($recipient != ''){
                        $mailTemplate->setDesignConfig(array('area' => 'backend'))
                            ->sendTransactional(
                                $template,
                                array(
                                    'name' => 'BridgeChannelAdvisor Module',
                                    'email' => Mage::getStoreConfig('trans_email/ident_general/email')
                                ),
                                $recipient,
                                null,
                                array(
                                    'messages' => nl2br($items)
                                )
                            );
                        if(!$mailTemplate->getSentSuccess()){

                            Mage::log('Can\'t send notification to '.$recipient);
                        }
                    }
                }

            }catch(Exception $e){

                Mage::log($e->getMessage());
                return false;
            }
        }

        return true;

    }
    
    public function checkSuperMapping(){
        $success = 1;
        $mess = array();
        $returnArr = array();
        //1 get all already mapped magento attribute sets
        $mappedAttributesSets = Mage::getModel('bridgechanneladvisor/type')->getCollection();
        $mappedSetIds = array();
        if(count($mappedAttributesSets) > 0){
            foreach($mappedAttributesSets as $mappedAttributesSet){
                $mappedSetIds[] = array('type_id' => $mappedAttributesSet->getTypeId(), 'attribute_set_id' => $mappedAttributesSet->getAttributeSetId());
            }
        }
    
        //2 get all super attributes by mapped attribute sets
        $superAttributes = array();
        $badSuperAttributes = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();

        if(count($badSuperAttributes) > 0){
            foreach($badSuperAttributes as $badSuperAttribute){
                $allow = '';
                $allow = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL &&
                    $badSuperAttribute->getIsVisible() &&
                    $badSuperAttribute->getIsConfigurable() &&
                    $badSuperAttribute->usesSource() &&
                    $badSuperAttribute->getIsUserDefined();
                if($allow){
                    $superAttributes[] = $badSuperAttribute->getId();
                }
            }
    
            if(count($superAttributes) > 0){
                $superAttributes = array_unique($superAttributes);
                $superAttributes = implode(',',$superAttributes);
                $setsAttributes = array();
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $query = 'SELECT attribute_set_id, attribute_id FROM eav_entity_attribute WHERE attribute_id IN('
                    . $superAttributes.')';
    
                $setsAttributes = $readConnection->fetchAll($query);
    
                $checkingAttributes = array();
                if(count($setsAttributes) > 0){
                    //3 check all attributes already mapped
                    $pairAttrsCollection = Mage::getModel('bridgechanneladvisor/pair')->getCollection();
                    if(count($pairAttrsCollection) > 0){
                        foreach($mappedSetIds as $mappedSetId){
                            $mappedAttributes = array();
                            foreach($pairAttrsCollection as $pairAttrs){
                                if($pairAttrs->getTypeId() == $mappedSetId['type_id']){
                                    $mappedAttributes[] = $pairAttrs->getMageAttributeId();
                                }
                            }
    
                            $checkingAttributes = array();
                            foreach($setsAttributes as $setsAttribute){
                                if($setsAttribute['attribute_set_id'] == $mappedSetId['attribute_set_id']){
                                    $checkingAttributes[] = $setsAttribute['attribute_id'];
                                }
                            }
    
                            $result1 = array();
                            $checkingAttributes = array_unique($checkingAttributes);
                            if(count($mappedAttributes) > 0){
                                $mappedSuperAttributes = array_intersect($mappedAttributes, $checkingAttributes);
                                $result1 = array_diff($checkingAttributes, $mappedSuperAttributes);
                            }else{
                                $result1 = $checkingAttributes;
                            }
    
                            if(count($result1) > 0){
                                $finalArr = '';
                                $cnt = 0;
                                foreach($result1 as $attributeId){
                                    $attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
                                    if($cnt == 0){
                                        $finalArr = $finalArr. ' ' .$attribute->getFrontendLabel();
                                    }else{
                                        $finalArr = $finalArr. ', ' .$attribute->getFrontendLabel();
                                    }
                                    $cnt++;
                                }
                                $attributeSetModel = Mage::getModel("eav/entity_attribute_set")->load($mappedSetId['attribute_set_id']);
                                $attributeSetName  = $attributeSetModel->getAttributeSetName();
                                $mess[] = 'Attribute Set Name "'.$attributeSetName.'". Please add to mapping next magento attributes: '.$finalArr;
                                $success = 0;
                            }
                        }
                    }else{
                        $success = 0;
                        $mess[] = 'No mapped attributes';
                    }
                }else{
                    $success = 0;
                    $mess[] = 'No Supper Attributes connected with AttributeSets';
                }
            }else{
                $success = 0;
                $mess[] = 'No Supper Attributes';
            }
    
        }else{
            $success = 0;
            $mess[] = 'No Supper Attributes';
        }
    
        $returnArr[0] = $success;
        $returnArr[1] = $mess;
    
            return $returnArr;

    }

    public function checkRelationships() {
        /** @var QS_BridgeChannelAdvisor_Model_Mysql4_Relationships_Collection $collection */
        $relationships = Mage::getResourceModel('bridgechanneladvisor/relationships_collection');
        if(!$relationships->getSize()){
                return $this->__('Please import relationships from the ChannelAdvisor if you want to import or export configurable products.');
        }

        return false;
    }
}