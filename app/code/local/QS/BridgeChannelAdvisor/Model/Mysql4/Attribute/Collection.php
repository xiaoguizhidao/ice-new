<?php
/**
 * ChannelAdvisor Attributes collection
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init("bridgechanneladvisor/attribute");
    }

    /**
     * Join Attribute Set data
     *
     * @return QS_BridgeChannelAdvisor_Model_Mysql4_Attribute_Collection
     */
    public function _joinCategories()
    {
        $this->getSelect()
            ->join(
                array('relations'=>$this->getTable('bridgechanneladvisor/relation')),
                'main_table.attribute_id=relations.attribute_id',
                array('category_id' => 'relations.category_id'));

        $this->getSelect()
            ->join(
                array('cats'=>$this->getTable('bridgechanneladvisor/category')),
                'relations.category_id=cats.category_id',
                array('category_name' => 'cats.category_name'));

        return $this;
    }

}
