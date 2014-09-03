<?php
/**
 * @category   Oro
 * @package    Oro_Delmar
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */


/**
 * Indexer model to match Vendor SKU with Product Id
 */
class Oro_Delmar_Model_Indexer_Vendorsku extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('delmar/indexer_vendorsku');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('tag')->__('Vendor SKU');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('tag')->__('Rebuild Vendor SKU Index');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $this->_registerCatalogProductSaveEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;
        }
    }

    /**
     * Register data required by catalog product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $event->getDataObject();

        $reindex = $product->getForceReindexRequired()
            || $product->dataHasChangedFor(Oro_Delmar_Helper_Data::ATTRIBUTE_VENDOR_SKU);

        if (!$product->isObjectNew() && $reindex) {
            $event->addNewData('vendorsku_reindex_required', true);
        }
    }

    /**
     * Register data required by catalog product delete process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData('vendorsku_reindex_delete', true);
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $this->callEventHandler($event);
    }

    /**
     * Fetches product ids associated with vendor sku
     *
     * @param string|string[] $vendorSku
     * @return array
     */
    public function getProductIds($vendorSku)
    {
        return $this->getResource()->fetchProductIds($vendorSku);
    }
}
