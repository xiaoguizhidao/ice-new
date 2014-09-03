<?php

class Oro_Orderstat_Model_Export_Order_Accuratime extends Oro_Orderstat_Model_Export_Order_Abstract
{

    const PRODUCT_DISTRIBUTOR = 'accuratime';

    protected $_exportFields = null;
    protected $_orderFileName = null;

    protected $_attribute = null;

    protected $_helper;

    public function getHelper()
    {
        if(!$this->_helper){
            $this->_helper = Mage::helper('orderstat/accuratime');
        }
        return $this->_helper;
    }

    public function isActive()
    {
        return $this->getHelper()->getGeneralConfig('is_active', true);
    }
    /*
     * Export file to vendor
     *
     * @param file stream.
     */
    public function export($fileStream)
    {
        $filePath = $this->getIo()->tmpFile('order_export_'.static::PRODUCT_DISTRIBUTOR,false);
        $to = $this->getHelper()->getOrderConfig('email_to');
        $from = $this->getHelper()->getOrderConfig('email_from');
        $subject = 'Orders From Ice.com';
        $mail = new Zend_Mail();
        $mail->setFrom($from, 'Ice.com');
        $mail->addTo($to);
        $mail->setSubject($subject);
        $mail->setBodyText('Order from Ice.com');
        $this->getHelper()->log('Exporting Accuratime Order file: '. $filePath);
        $at = new Zend_Mime_Part(file_get_contents($filePath));
        $at->type = 'application/csv';
        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding = Zend_Mime::ENCODING_8BIT;
        $at->filename = 'ICEACCURATIMEORDERS'.time().'.csv';

        $mail->addAttachment($at);
        $mail->send();
        return $this;

    }

    /**
     * Gets order fields for export
     *
     * @return array
     */
    public function getExportFields()
    {
        if ($this->_exportFields === null) {
            $this->_exportFields = array();
            $orderConfigXML = new Varien_Simplexml_Element(
                file_get_contents(Mage::getModuleDir('etc', 'Oro_Orderstat') . DS . 'accuratime_order_export.xml')
            );

            foreach ($orderConfigXML->children() as $child) {
                $this->_exportFields[(string)$child->name] = $child->asArray();
            }
        }

        return $this->_exportFields;
    }
}