<?php
/**
 * @category   Oro
 * @package    Oro_Orderstat
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */


/**
 * Indexer resource model to match Vendor SKU with Product Id
 */
class Oro_Orderstat_Model_Resource_Indexer_Vendorsku extends Mage_Catalog_Model_Resource_Product_Indexer_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('orderstat/index', 'product_id');
    }

    /**
     * Process product save.
     * Method is responsible for index support when product was saved.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Tag_Model_Resource_Indexer_Summary
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['tag_reindex_required'])) {
            return $this;
        }

        $this->saveIndex(array(array(
            'product_id' => $event->getEntityPk(),
            'vendor_sku' => $event->getDataObject()->getData(Oro_Orderstat_Helper_Data::ATTRIBUTE_VENDOR_SKU)
        )));

        return $this;
    }

    /**
     * Process product delete.
     * Method is responsible for index support when product was deleted
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Tag_Model_Resource_Indexer_Summary
     */
    public function catalogProductDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!$data['vendorsku_reindex_delete']) {
            return $this;
        }

        return $this->deleteIndex(array($event->getEntityPk()));
    }

    /**
     * Update one or more indexes
     *
     * @param array $data
     * @return $this
     */
    public function saveIndex(array $data)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->insertOnDuplicate($this->getMainTable(), $data);

        return $this;
    }

    /**
     * Delete indexes
     *
     * @param array $ids
     * @return $this
     */
    public function deleteIndex(array $ids)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->delete($this->getMainTable(), array('product_id in (?)' => $ids));

        return $this;
    }

    /**
     * Reindex all
     *
     * @return Mage_Tag_Model_Resource_Indexer_Summary
     */
    public function reindexAll()
    {
        $this->_getWriteAdapter()->delete($this->getMainTable());

        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', Oro_Orderstat_Helper_Data::ATTRIBUTE_VENDOR_SKU);

        $select = Mage::getResourceModel('catalog/product_collection')->getSelect();

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('product_id' => 'e.entity_id', 'type_id' => 'e.type_id'));

        $select
            ->join(
                array('vsku' => $attribute->getBackendTable()),
                'vsku.entity_id = e.entity_id and attribute_id = ' . $attribute->getId(),
                array('vendor_sku' => 'vsku.value')
            )
            ->where('vsku.`value` is not null')
            ->where("vsku.`value` != '0'")
            ->group('product_id');

        $query = $this->_getWriteAdapter()->insertFromSelect(
            $select,
            $this->getMainTable(),
            array('product_id', 'type_id', 'vendor_sku')
        );

        $this->_getWriteAdapter()->query($query);

        return $this;
    }

    /**
     * Fetches product ids associated with vendor sku
     *
     * @param string|string[] $vendorSku
     * @return array
     */
    public function fetchProductIds($vendorSku)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('i' => $this->getMainTable()), array('product_id', 'type_id', 'vendor_sku'))
            ->joinLeft(array('r' => $this->getTable('catalog/product_relation')),
                'i.product_id = r.child_id',
                array('parent_id' => 'r.parent_id')
            )
            ->where('i.vendor_sku IN(?)', $vendorSku);

        return $this->_getReadAdapter()->fetchAll($select);
    }
}
