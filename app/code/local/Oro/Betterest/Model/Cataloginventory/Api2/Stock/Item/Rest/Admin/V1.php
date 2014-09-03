<?php
/**
 * API2 class for stock item (guest)
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Ice.com
 */
class Oro_Betterest_Model_Cataloginventory_Api2_Stock_Item_Rest_Admin_V1 extends Mage_CatalogInventory_Model_Api2_Stock_Item_Rest
{
    protected function _retrieve()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockData = array();
        $product = Mage::getModel('catalog/product')
                    ->loadByAttribute('sku',$this->getRequest()->getParam('sku'));
        if(is_object($product) && $product->getId()){
             $stockData = Mage::getModel('cataloginventory/stock_item')->assignProduct($product)->getData();
            $stockData['sku'] = $this->getRequest()->getParam('sku');
            $stockData['name'] = Mage::helper('core')->__($product->getName());
            $stockData['image'] = $this->_getProductImageUrl($product);
            $stockData['description'] = $product->getDescription();
        }

        if($product->isConfigurable()){
            $stockData['options'] = $this->_getChildProducts($product);
            if(empty($stockData['options'])){
                $stockData['is_in_stock'] = 0;
            }
        }else{
            $stockData['ring_size'] = $product->getAttributeText('iceus_ring_size');
        }

        return $stockData;
    }

    protected function _update(array $data)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$this->getRequest()->getParam('sku'));
        // check if the product is loaded
        if($product->getId() && isset($data['qty']) && is_numeric($data['qty'])){
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->assignProduct($product)
                      ->setData('stock_id', 1)
                      ->setData('store_id', Mage::app()->getStore()->getStoreId());

            $newQty = $stockItem->getQty() - $data['qty'];
            if($newQty < 0){
                $newQty = 0;
            }

            /* @var $validator Mage_CatalogInventory_Model_Api2_Stock_Item_Validator_Item */
            $validator = Mage::getModel('cataloginventory/api2_stock_item_validator_item', array(
                'resource' => $this
            ));

            if (!$validator->isValidData($data)) {
                foreach ($validator->getErrors() as $error) {
                    $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                }
                $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
            }

            $stockItem->setData('qty', $newQty)
                      ->setData('is_in_stock', $newQty > 0 ? 1 : 0)
                      ->setData('manage_stock', 1)
                      ->setData('use_config_manage_stock', 0)
                      ->save();

            try {
                $stockItem->save();
            } catch (Mage_Core_Exception $e) {
                $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            } catch (Exception $e) {
                $this->_critical(self::RESOURCE_INTERNAL_ERROR);
            }

        }else{
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
    }


    protected function _retrieveCollection()
    {

        $collection = $this->_getCollectionForRetrieve();
        $products = array();

        foreach($collection->getItems() as $product){
            $options = $this->_getChildProducts($product);
            $products[] = array(
                'product_id' => $product->getId(),
                'item_id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => Mage::helper('core')->__($product->getName()),
                'description' => $product->getDescription(),
                'image' => $this->_getProductImageUrl($product),
                'is_in_stock' => (empty($options)) ? 0 : $product->getStockItem()->getIsInStock(),
                'qty' => (int) $product->getStockItem()->getQty(),
                'options' => $options
            );

        }

        return $products;

    }

    protected function _getCollectionForRetrieve()
    {
        $category = Mage::getModel('catalog/category');
        $category->setStoreId(Mage::app()->getStore('ice_us')->getStoreId());
        
        if($this->getRequest()->getParam('category_id')){
            $category->load($this->getRequest()->getParam('category_id'));
        }else{
            $category->load(Mage::app()->getStore('ice_us')->getRootCategoryId());
        }

        $layer = Mage::getSingleton('catalog/layer');
        $layer->setCurrentCategory($category);
        $collection = $layer->getProductCollection()
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('image');

        $this->_applyCollectionModifiers($collection);
        
        return $collection;
    }


    protected function _getChildProducts($product)
    {
        $options = array();
        $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
        $simpleCollection = $conf->getUsedProductCollection()
            ->addAttributeToSelect('iceus_ring_size')
            ->addFilterByRequiredOptions();
        foreach($simpleCollection as $simpleProd){
            $simpleStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProd);
            $options[] = array(
                'sku' => $simpleProd->getSku(),
                'product_id' => $simpleStockItem->getProductId(),
                'qty' => $simpleStockItem->getQty(),
                'is_in_stock' => $simpleStockItem->getIsInStock(),
                'ring_size' => $simpleProd->getAttributeText('iceus_ring_size')
            );
        }
        return $options;
    }

    protected function _getProductImageUrl($product)
    {
        return (string) Mage::helper('catalog/image')->init($product, 'image');
    }
}