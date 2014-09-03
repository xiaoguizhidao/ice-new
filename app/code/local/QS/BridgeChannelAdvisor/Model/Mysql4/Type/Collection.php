<?php
/**
 * ChannelAdvisor Types collection
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init("bridgechanneladvisor/type");
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
        if ($field == 'attribute_set_name') {
            $conditionSql = $this->_getConditionSql(
                'set.attribute_set_name', $condition
            );
            $this->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
            return $this;
        }elseif ($field == 'channel_category_name') {
            $conditionSql = $this->_getConditionSql(
                'ca_names.category_name', $condition
            );
            $this->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }


    /**
     * Init collection select
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Type_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinAttributeSet();
        $this->_joinChannelAdvisorCategoryName();
        return $this;
    }

   /**
    * Get SQL to get record count
    *
    * @return Varien_Db_Select
    */
   public function getSelectCountSql()
   {
       $this->_renderFilters();
       $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($this->getSelect());
       return $paginatorAdapter->getCountSelect();
   }

    /**
     * Add items count by type
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Type_Collection
     */
    public function addItemsCount()
    {
        $this->getSelect()
            ->joinLeft(
                array('items'=>$this->getTable('bridgechanneladvisor/items')),
                'main_table.type_id=items.type_id',
                array('items_total' => new Zend_Db_Expr('COUNT(items.item_id)')))
            ->group('main_table.type_id');
        return $this;
    }

    /**
     * Join Attribute Set data
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Type_Collection
     */
    public function _joinAttributeSet()
    {
        $this->getSelect()
            ->join(
                array('set'=>$this->getTable('eav/attribute_set')),
                'main_table.attribute_set_id=set.attribute_set_id',
                array('attribute_set_name' => 'set.attribute_set_name'));
        return $this;
    }

    /**
     * Join ChannelAdvisor Category Name
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Type_Collection
     */
    public function _joinChannelAdvisorCategoryName()
    {
        $this->getSelect()
            ->join(
                array('ca_names'=>$this->getTable('bridgechanneladvisor/category')),
                'main_table.category_id=ca_names.category_id',
                array('channel_category_name' => 'ca_names.category_name'));
        return $this;
    }
}
