<?php
/**
 * @category   Oro
 * @package    Oro_Diamondcandles
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */

class Oro_Diamondcandles_Model_Import extends Mage_Core_Model_Abstract
{
    const XML_PATH_FILE_PATH = 'diamondcandles/general/file';
    const XML_PATH_EMAIL_ADDRESS = 'diamondcandles/general/email';
    const XML_PATH_PHONE = 'diamondcandles/general/phone';
    const LOG_FILE = 'diamondcandles.log';
    public function import()
    {
        try{

            $file = Mage::getBaseDir('var').'/'.Mage::getStoreConfig(self::XML_PATH_FILE_PATH);
            // strip out any double slashes
            $file = str_replace('//', '/', $file);
            if(file_exists($file)){
                $csv = new Varien_File_Csv();
                $csv->setDelimiter("	");
                $data = $csv->getData($file);
                $orderModel = Mage::getModel('sales/order');
                $storeId = Mage::app()->getStore('ice_us')->getStoreId();
                $productModel = Mage::getModel('catalog/product');

                for($i=1; $i<count($data); $i++) {
                    $orderData = $data[$i];
                    $poNumber = $orderData[0];
                    $order = $orderModel->getCollection();
                    $order->getSelect()->join(array('order_payment' => 'sales_flat_order_payment'), 'main_table.entity_id=order_payment.parent_id');
                    $order->addAttributeToFilter('order_payment.po_number', 'DC'.$poNumber)
                        ->addAttributeToSelect('custom_merchant')
                        ->addAttributeToFilter('main_table.custom_merchant', 'diamond_candles');
                    $orderItem = $order->getFirstItem();

                    // if there is no item, it's a new order
                    if(!$orderItem->getId() && (strtolower($orderData[17]) != 'cancelled')){

                        $region = Mage::getModel('directory/region')->loadByCode($orderData[8], 'US');
                        $phone = (!empty($orderData[13])) ?  $orderData[13] : Mage::getStoreConfig(self::XML_PATH_PHONE);

                        $newOrder = array(
                            'currency' => 'USD',
                            'account' => array(
                                'group_id' => Mage_Customer_Model_Group::NOT_LOGGED_IN_ID,
                                'email' => Mage::getStoreConfig(self::XML_PATH_EMAIL_ADDRESS),
                            ),
                            'shipping_method' => $this->_getMappedShippingMethod($orderData[15]),
                            'comment' => array(
                                'customer_note' => 'Order was created by DiamondCandles nightly import'
                            ),
                            'billing_address' => array(
                                'firstname' => $orderData[5],
                                'lastname' => $orderData[6],
                                'city' => $orderData[7],
                                'region_id' => $region->getId(),
                                'street' => array(
                                    '0' => $orderData[9],
                                    '1' => $orderData[10],
                                ),
                                'country_id' => 'US',
                                'postcode' => $orderData[12],
                                'telephone' => $phone

                            ),
                        );

                        $orderInfo = array(
                            'shipping_method' => $this->_getMappedShippingMethod($orderData[16]),
                            'items' => array(
                                array(
                                    'sku' => $orderData[2],
                                    'qty' => $orderData[3]
                                )
                            )
                        );

                        $newOrder['shipping_address'] = $newOrder['billing_address'];

                        $quoteObj = Mage::getModel('sales/quote')
                                       ->setCustomerEmail(Mage::getStoreConfig(self::XML_PATH_EMAIL_ADDRESS));
                        $quoteObj->setStoreId($storeId);
                        $quoteObj->reserveOrderId();
                        foreach($orderInfo['items'] as $item){
                            $productObj = $productModel->load($productModel->getIdbySku($item['sku']));
                            if(is_object($productObj) && $productObj->getId()){
                                $quoteObj->addProduct($productObj, (int) $item['qty']);
                            }
                        }
                        $quoteObj->setData('custom_merchant', 'diamond_candles');
                        $quoteObj->getShippingAddress()->addData($newOrder['shipping_address']);
                        $quoteObj->getBillingAddress()->addData($newOrder['billing_address']);
                        $quoteObj->getShippingAddress()->implodeStreetAddress();
                        $quoteObj->getShippingAddress()
                            ->setShippingMethod($orderInfo['shipping_method'])
                            ->setCollectShippingRates(true)->save();

                        $quoteObj->collectTotals();

                        $quoteObj->save();
                        $quotePaymentObj = $quoteObj->getPayment();
                        $quotePaymentObj->setMethod('purchaseorder');
                        $quotePaymentObj->setPoNumber('DC'.$orderData[0]);

                        $quoteObj->setPayment($quotePaymentObj);
                        $quoteObj->save();

                        $service = Mage::getModel('sales/service_quote', $quoteObj);
                        $service->submitAll();

                    }else{
                        $orderItem->load($orderItem->getParentId());
                        // cancel the order
                        if($orderItem->getId() && strtolower($orderData[17]) == 'cancelled'){
                            $orderItem
                                ->setState(Mage_Sales_Model_Order::STATE_CANCELED,true)
                                ->setStatus(Mage_Sales_Model_Order::STATE_CANCELED,true)
                                ->save();
                        }
                    }
                }

                // once we've imported all of the orders, rename the file
                $this->_renameFile($file);

            }

        }catch(Exception $e){
            $this->_log($e->getMessage());
        }

    }

    protected function _getMappedShippingMethod($shippingCode)
    {
        $helper = Mage::helper('betterest/data');
        $shippingMap = $helper->getShippingMap()->toArray();
        $shippingMethod = '';
        $groundShipping = '';
        foreach($shippingMap['items'] as $item){
            if(trim($item['external_shipping_method']) == trim($shippingCode)){
                $shippingMethod = trim($item['internal_shipping_method']);
            }

            if(trim($item['external_shipping_method']) == 'GROUND'){
                $groundShipping = trim($item['internal_shipping_method']);
            }
        }
        return ($shippingMethod != '') ? $shippingMethod : $groundShipping;
    }

    protected function _renameFile($filePath)
    {
        $fileParts = explode('/', $filePath);
        $fileName = $fileParts[count($fileParts) -1];
        $fileNameParts = explode('.', $fileName);
        $name = $fileNameParts[0];
        $ext = $fileNameParts[1];
        $rename = $name.'-'.date("Y-m-d-H-i-s", time());
        $newFile = str_replace($name,$rename, $filePath);
        rename($filePath,$newFile);
    }

    protected function _log($message)
    {
        Mage::log($message, null, self::LOG_FILE);
    }

}