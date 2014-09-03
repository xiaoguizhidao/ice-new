<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Rewrite class to provide Schedule functionsl and possibility to import configurable products
 */
class Oro_Dataflow_Model_Catalog_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{

    /** @var $_collection Oro_Dataflow_Model_Resource_Catalog_Product_Collection  */
    protected $_collection = null;


    /**
     * @param Oro_Dataflow_Model_Schedule_Abstract $schedule
     * @return Oro_Dataflow_Model_Resource_Catalog_Product_Collection
     */
    protected function _getBatchCollectionForLoad(Oro_Dataflow_Model_Schedule_Abstract $schedule)
    {
        $size        = $schedule->getData('batch_size');
        $offset      = (int) $schedule->getData('rows_complete');
        $productsIds = $schedule->getIds();

        /** @var Oro_Dataflow_Model_Resource_Catalog_Product_Collection $collection */
        $collection = Mage::getResourceModel('oro_dataflow/catalog_product_collection');
        $collection->setStoreId($this->getStoreId());
        $collection->addStoreFilter($this->getStoreId());
        $collection->setLimit($size, $offset);

        if (!empty($productsIds)) {
            $collection->addIdFilter($productsIds);
        }

        return $collection;
    }

    /**
     * @param string $entityType
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getCollectionForLoad($entityType)
    {
        /** @var Mage_Dataflow_Model_Convert_Profile $profile */
        $profile     = $this->getProfile();
        $data        = $profile->getDataflowProfile();
        /** @var Oro_Dataflow_Model_Schedule_Abstract $schedule */
        $schedule    = isset($data['schedule'])? $data['schedule'] : false;

        if ($schedule instanceof Oro_Dataflow_Model_Schedule_Interface) {
            $collection = $this->_getBatchCollectionForLoad($schedule);
        } else {
            $collection = parent::_getCollectionForLoad($entityType);
        }

        $this->_collection = $collection;

        return $collection;
    }

    /**
     * Load product collection Id(s)
     */
    public function load()
    {
        $filters = $this->_parseVars();

        $stockStatus = isset($filters['is_in_stock']) ? $filters['is_in_stock'] : false ;
        if ($stockStatus) {
            $stockStatus = $stockStatus == 'in_stock' ? 1 : 0;
            $stockArray = array();
            $stockArray['alias']       = 'is_in_stock';
            $stockArray['attribute']   = 'cataloginventory/stock_item';
            $stockArray['field']       = 'is_in_stock';
            $stockArray['bind']        = 'product_id=entity_id';
            $stockArray['cond']        = "{{table}}.is_in_stock = '{$stockStatus}'";
            $stockArray['joinType']    = 'inner';

            $this->setJoinField($stockArray);
        }

        parent::load();

        // store correct total number of rows to schedule model
        $data = $this->getProfile()->getDataflowProfile();

        /** @var Oro_Dataflow_Model_Schedule_Abstract $schedule */
        $schedule = isset($data['schedule'])? $data['schedule'] : false;
        if ($schedule && $data['schedule'] instanceof Oro_Dataflow_Model_Schedule_Interface && $this->_collection) {
            $size        = $schedule->getData('batch_size');
            $offset      = (int) $schedule->getData('rows_complete');

            $collectionCount = $this->_collection->getTotalCount();
            $rowsComplete    = $size + $offset;
            if ($rowsComplete > $collectionCount) {
                $rowsComplete = $collectionCount;
            }

            $schedule->setData('rows_total', $collectionCount);
            $schedule->setData('rows_complete', $rowsComplete);
        }

        return $this;
    }

    /**
     * Save product (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);
        $new = true; // fix for duplicating attributes error

        if ($productId) {
            $product->load($productId);
            $new = false; // fix for duplicating attributes error
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setProductTypeInstance($product);

        //================================================
        // this part handles configurable products and links
        //================================================

        if ($importData['type'] == 'configurable') {
            $product->setCanSaveConfigurableAttributes(true);
            $configAttributeCodes = $this->userCSVDataAsArray($importData['config_attributes']);
            $usingAttributeIds = array();
            foreach($configAttributeCodes as $attributeCode) {
                $attribute = $product->getResource()->getAttribute($attributeCode);
                if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract && $product->getTypeInstance()->canUseAttribute($attribute)) {
                    if ($new) { // fix for duplicating attributes error
                        $usingAttributeIds[] = $attribute->getAttributeId();
                    }
                }
            }
            if (!empty($usingAttributeIds)) {
                $product->getTypeInstance()->setUsedProductAttributeIds($usingAttributeIds);
                $product->setConfigurableAttributesData($product->getTypeInstance()->getConfigurableAttributesAsArray());
                $product->setCanSaveConfigurableAttributes(true);
                $product->setCanSaveCustomOptions(true);
            }
            if (isset($importData['associated'])) {
                $product->setConfigurableProductsData($this->skusToIds($importData['associated'], $product));
            }
        }

        /**
         * Init product links data (related, upsell, crosssell, grouped)
         */
        if (isset($importData['related'])) {
            $linkIds = $this->skusToIds($importData['related'], $product);
            if (!empty($linkIds)) {
                $product->setRelatedLinkData($linkIds);
            }
        }
        if (isset($importData['upsell'])) {
            $linkIds = $this->skusToIds($importData['upsell'], $product);
            if (!empty($linkIds)) {
                $product->setUpSellLinkData($linkIds);
            }
        }
        if (isset($importData['crosssell'])) {
            $linkIds = $this->skusToIds($importData['crosssell'], $product);
            if (!empty($linkIds)) {
                $product->setCrossSellLinkData($linkIds);
            }
        }
        if (isset($importData['grouped'])) {
            $linkIds = $this->skusToIds($importData['grouped'], $product);
            if (!empty($linkIds)) {
                $product->setGroupedLinkData($linkIds);
            }
        }

        //================================================





        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }

        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }

    /**
     * @param string $data
     * @return array
     */
    protected function userCSVDataAsArray($data)
    {
        return explode(',', str_replace(" ", "", $data));
    }

    /**
     * @param string $userData
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function skusToIds($userData,$product)
    {
        $productIds = array();
        foreach ($this->userCSVDataAsArray($userData) as $oneSku) {
            if (($a_sku = (int)$product->getIdBySku($oneSku)) > 0) {
                parse_str("position=", $productIds[$a_sku]);
            }
        }
        return $productIds;
    }

}
