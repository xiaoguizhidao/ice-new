<?php

class Oro_Orderstat_Model_Export_Order_Delmar extends Oro_Orderstat_Model_Export_Order_Abstract
{
    const PRODUCT_DISTRIBUTOR = 'delmar';
    protected $_helper;

    public function getHelper()
    {
        if(!$this->_helper){
            $this->_helper = Mage::helper('orderstat/delmar');
        }
        return $this->_helper;
    }

    public function isActive()
    {
        return $this->getHelper()->getGeneralConfig('is_active', true);
    }

    public function export($file)
    {
        $this->getIo()->setHelper($this->getHelper());
        $result = $this->getIo()->connect($this->getHelper()->getOrderConfig('ftp_path'))
            ->send($this->getOrderFilename());
        $this->getIo()->close();


        return $result;
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
                file_get_contents(Mage::getModuleDir('etc', 'Oro_Orderstat') . DS . 'order_export.xml')
            );

            foreach ($orderConfigXML->children() as $child) {
                $this->_exportFields[(string)$child->name] = $child->asArray();
            }
        }

        return $this->_exportFields;
    }
}