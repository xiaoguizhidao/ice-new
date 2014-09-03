<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Model_Resource_Catalog_Product_Collection
    extends Mage_Catalog_Model_Resource_Product_Collection
{
    protected $_limitCount = null;
    protected $_limitOffset = null;

    /**
     * @param null|int $count
     * @param null|int $offset
     * @return $this
     */
    public function setLimit($count = null, $offset = null)
    {
        $this->_limitCount = $count;
        $this->_limitOffset = $offset;

        return $this;
    }

    /**
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($this->_limitCount, $this->_limitOffset), $this->_bindParams);
    }

    /**
     * @return string
     */
    public function getTotalCount()
    {
        /** @var Varien_Db_Select $select */
        $select = $this->_getAllIdsSelect();
        $select->reset(Varien_Db_Select::COLUMNS);
        $select->columns('COUNT(*)');

        return $this->getConnection()->fetchOne($select, $this->_bindParams);
    }
}
