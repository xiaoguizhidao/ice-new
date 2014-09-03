<?php
/**
 * ChannelAdvisor PrepareItems collection
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Prepareitem_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init("bridgechanneladvisor/prepareitem");
    }

    /**
     * Filter by store ids
     *
     * @param array|int $storeIds
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Prepareitem_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Join Product Name and Sku
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Prepareitem_Collection
     */
    public function _joinMageProductData()
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
            ->join(
                array('products' => $this->getTable('catalog/product')),
                'main_table.product_id=products.entity_id');

        return $this;
    }

    /**
     * Filter by product id
     *
     * @param array|int $storeIds
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Prepareitem_Collection
     */
    public function addProductIdFilter($prodId)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $prodId);
        return $this;
    }

}
