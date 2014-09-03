<?php

class Oro_Orderstat_Model_Import_Shipping_Accuratime extends Oro_Orderstat_Model_Import_Shipping_Abstract
{
    const FIELD_ORDER_NUMBER = 0;
    const FIELD_VENDOR_SKU = 1;
    const FIELD_QTY = 2;
    const FIELD_STATUS = 4;
    const FIELD_SHIP_DATE = 6;
    const FIELD_CARRIER = 12;
    const FIELD_TRACK_NUMBER = 14;
    const EMAIL_COMPLETED_FOLDER = '[Gmail]/All Mail';
    const EMAIL_FOLDER = 'INBOX';

    protected $_emailSearchFilters = array('to accuratimeorders@ice.com');
    protected $_filePath;
    protected $_fileName = 'ICEUSACCURATIMESHIPPED';
    protected $_configPath = Oro_Orderstat_Helper_Accuratime::CONFIG_PATH_SHIPMENT;

    protected $_carriers = array();
    protected $_lastProcessed = null;

    public function _construct()
    {
        parent::_construct();
        if($this->getHelper()->getShipmentConfig('email_from')){
            $this->_emailSearchFilters = array('to '. $this->getHelper()->getShipmentConfig('email_from'));
        }
    }
    public function update()
    {
        /*
         *
         * get shipping emails from iceorders@ice.com and save
         * to var/Orderstat/Import/datetimefile
         */
        try{
            $config = array(
                'host' => $this->getHelper()->getShipmentConfig('host'),
                'user' => $this->getHelper()->getShipmentConfig('email'),
                'password' => $this->getHelper()->getShipmentConfig('password'),
                'ssl' => 'SSL'
            );

            // connect to gmail
            $mStorage = new Zend_Mail_Storage_Imap($config);
            $m = new Zend_Mail_Protocol_Imap($config['host'], null, $config['ssl']);
            $m->login($config['user'], $config['password']);

            $m->select(static::EMAIL_FOLDER);
            $messageIds = $m->search($this->_emailSearchFilters);

            foreach($messageIds as $mId){
                $message = $mStorage->getMessage($mId);

                if(!$message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)){
                    if($message->isMultipart()){
                        $part = $message->getPart(2);

                        // decode the attatchment and save it
                        $content = base64_decode($part->getContent());
                        $newFile = $this->getFilePath('csv');
                        file_put_contents($newFile, $content);

                        // import the files data
                        $this->import($newFile);
                        // Mark email as read and
                        $mStorage->setFlags($mId, array(Zend_Mail_Storage::FLAG_SEEN));
                        $mStorage->moveMessage($mId,static::EMAIL_COMPLETED_FOLDER);
                    }else{
                        $this->getHelper()->log('Accuratime order email sent with no attachment with a subject of: '. $message->subject);
                    }
                }

            }
        }catch(Exception $e){
            $this->getHelper()->log($e->getMessage());
        }
    }

    public function import($file)
    {
        $shipmentData = array();
        $csv = new Varien_File_Csv();
        $csv->setDelimiter(',');
        $csv->setEnclosure('"');
        $rows = $csv->getData($file);
        // remove the headers
        array_shift($rows);
        foreach($rows as $row){
            if(!isset($shipmentData[$row[static::FIELD_ORDER_NUMBER]])){
                $shipmentData[$row[static::FIELD_ORDER_NUMBER]] = array(
                    'data' => array(),
                    'track' => array()
                );
            }

            $shipmentData[$row[static::FIELD_ORDER_NUMBER]]['items'][trim($row[static::FIELD_VENDOR_SKU])]
                = trim($row[static::FIELD_QTY]);
            $shipmentData[$row[static::FIELD_ORDER_NUMBER]]['tracks'][trim($row[static::FIELD_TRACK_NUMBER])] = array(
                'number' => trim($row[static::FIELD_TRACK_NUMBER]),
                'carrier_code' => '',
                'title' => ''
            );

        }

        foreach($shipmentData as $orderIncrementId => $orderData){
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if(!$order->getId()){
                $this->getHelper()->log(
                    "ERROR creating shipment for order #{$orderIncrementId}, No order found in Magento",
                    true
                );
                continue;
            }
            try{
                $shipment = $this->_createShipment($order, $orderData);
                $shipment->getOrder()
                    ->setStatus('processing_payment')
                    ->setSyncStatus(Oro_Orderstat_Helper_Data::ORDER_SYNC_STATUS_SHIPPED);

                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($order)
                    ->save();

                if($this->getHelper()->getModuleConfig($this->_configPath.'/capture',true)){
                    $this->_createInvoice($order);
                    $order->save();
                }

                $this->getHelper()->log("Shipment created for order #{$orderIncrementId}");
            } catch(Exception $e) {
                Mage::logException($e);
                $this->getHelper()->log(
                    "ERROR creating shipment for order #{$orderIncrementId}. " .$e->getMessage(),
                    true);
            }
        }

    }

    public function getHelper()
    {
        return Mage::helper('orderstat/accuratime');
    }

    public function getFilePath($ext = 'csv')
    {
        if(!$this->_filePath){
            $this->_filePath = Mage::getBaseDir('var').DS.'Orderstat/Import/'.$this->_fileName.time();
        }
        return $this->_filePath.'.'.$ext;
    }


}