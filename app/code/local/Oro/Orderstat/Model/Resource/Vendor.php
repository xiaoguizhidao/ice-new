<?php

class Oro_Orderstat_Model_Resource_Vendor extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init("orderstat/vendor", "vendor_id");
    }

    public function loadByName($object, $name)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
                    ->from($this->getMainTable())
                    ->where('vendor_name = ?', $name);
        $data = $adapter->fetchRow($select);
        if($data){
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }

    public function loadByCode($object, $code)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
                    ->from($this->getMainTable())
                    ->where('vendor_code = ?', $code);
        $data = $adapter->fetchRow($select);
        if($data){
            $object->setData($data);
        }
        return $this;
    }
}