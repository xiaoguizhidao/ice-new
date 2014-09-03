<?php
/**
 * ChannelAdvisor Items collection
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init("bridgechanneladvisor/item");
    }

    /**
     * Filter by store ids
     *
     * @param array|int $storeIds
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Item_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     * @param string $field
     * @param null|string|array $condition
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition=null)
    {
        if ($field == 'name') {
            $conditionSql = $this->_getConditionSql(
                $this->getConnection()->getIfNullSql('p.value', 'p_d.value'), $condition
            );
            $this->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Join Product Name that already uploaded to ChannelAdvisor Content
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Item_Collection
     */
    public function _joinMageProductName()
    {
        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
        $attribute = Mage::getSingleton('eav/config')->getAttribute($entityType->getEntityTypeId(),'name');

        $joinConditionDefault =
            sprintf("p_d.attribute_id=%d AND p_d.store_id='0' AND main_table.product_id=p_d.entity_id",
                $attribute->getAttributeId()
            );
        $joinCondition =
            sprintf("p.attribute_id=%d AND p.store_id=main_table.store_id AND main_table.product_id=p.entity_id",
                $attribute->getAttributeId()
            );

        $this->getSelect()
            ->joinLeft(
                array('p_d' => $attribute->getBackend()->getTable()),
                $joinConditionDefault,
                array());

        $this->getSelect()
            ->joinLeft(
                array('p' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array('name' => $this->getConnection()->getIfNullSql('p.value', 'p_d.value')));

        $this->getSelect()
            ->joinLeft(
                array('types' => $this->getTable('bridgechanneladvisor/types')),
                'main_table.type_id=types.type_id');

        return $this;
    }

}
