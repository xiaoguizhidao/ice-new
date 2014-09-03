<?php
/**
 * API2 class for orders (admin)
 *
 * @category   Oro
 * @package    Oro_Betterest
 * @author     Ice.com Team
 */
class Oro_Betterest_Model_Sales_Api2_Order_Rest_Admin_V1 extends Mage_Sales_Model_Api2_Order_Rest
{

		protected function _retrieve(){
			$order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));

			$payment = $order->getPayment();
			$shippingAdd = $order->getShippingAddress();
			$billingAdd = $order->getBillingAddress();
			$orderData = array(
				'id' => $order->getId(),
				'po_number' => $order->getPayment()->getPoNumber(),
				'order_number' => $order->getIncrementId(),
				'shipping_method' => $order->getShippingDescription(),
				'grand_total' => $order->getGrandTotal(),
				'shipping_amount' => $order->getShippingAmount(),
				'tax_amount' => $order->getTaxAmount(),
				'tracking' => $this->_getTrackingNumbers($order),
				'shipment' => $this->_getAddressInfo($shippingAdd),
				'billing' => $this->_getAddressInfo($billingAdd),
				'items' => $this->_getOrderItems($order)
			);

			return array($orderData);

		}

	protected function _create($order)
	{
		try{

			$storeId = Mage::app()->getStore('ice_us')->getStoreId();
			$productModel = Mage::getModel('catalog/product');
            $orderModel = Mage::getModel('sales/order');

            $orderCollection = $orderModel->getCollection();
            $orderCollection->getSelect()->join(array('order_payment' => 'sales_flat_order_payment'), 'main_table.entity_id=order_payment.parent_id');
            $orderCollection->addAttributeToFilter('order_payment.po_number', 'DC'.$order['order']['orderid'])
                ->addAttributeToSelect('custom_merchant')
                ->addAttributeToFilter('main_table.custom_merchant', 'diamond_candles');
            $orderItem = $orderCollection->getFirstItem();
            // make sure we're not adding duplicate orders
            if(!$orderItem->getId()){
                $shipment = $order['shipment'];
                if(!isset($shipment['country_id']) && isset($shipment['country'])){
                    $shipment['country_id'] = $shipment['country'];
                }

                if(!isset($shipment['region_id']) && isset($shipment['state'])){
                    $region = Mage::getModel('directory/region')->loadByCode($shipment['state'], $shipment['country_id']);
                    $shipment['region_id'] = $region->getId();
                }

                $orderData = $order['order'];
                $orderItems = $orderData['items'];
                $shippingMethod = $this->_getMappedShippingMethod($shipment['serviceLevel']);
                $quoteObj = Mage::getModel('sales/quote')->setCustomerEmail($shipment['email']);

                $quoteObj->setStoreId($storeId);
                $quoteObj->reserveOrderId();

                foreach($orderItems as $item){
                    $productObj = $productModel->load($productModel->getIdbySku($item['sku']));
                    if(is_object($productObj) && $productObj->getId()){
                        $quoteItem = Mage::getModel('sales/quote_item')->setProduct($productObj);
                        $quoteItem->setStoreId($storeId);
                        $quoteItem->setQuote($quoteObj);
                        $quoteItem->setQty($item['qty']);
                        $quoteItem->setData('iceus_ring_size', $productObj->getIceusRingSize());
                        $quoteObj->addItem($quoteItem);
                    }
                }
                $quoteObj->setData('custom_merchant', 'diamond_candles');
                $quoteObj->getShippingAddress()->addData($shipment);
                $quoteObj->getBillingAddress()->addData($shipment);
                $quoteObj->getShippingAddress()->implodeStreetAddress();
                $quoteObj->getShippingAddress()
                    ->setShippingMethod($shippingMethod)
                    ->setCollectShippingRates(true)->save();

                $quoteObj->collectTotals();

                $quoteObj->save();
                $quotePaymentObj = $quoteObj->getPayment();
                $quotePaymentObj->setMethod('purchaseorder');
                $quotePaymentObj->setPoNumber('DC'.$orderData['orderid']);
                $quoteObj->setPayment($quotePaymentObj);
                $quoteObj->save();

                $service = Mage::getModel('sales/service_quote', $quoteObj);
                $service->submitAll();
            }
			exit();
    } catch (Mage_Core_Exception $e) {
        $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
    } catch (Exception $e) {
       $this->_critical(self::RESOURCE_INTERNAL_ERROR);
    }
	}

	protected function _multiCreate(array $data)
	{
		foreach($data as $order){
			$this->_create($order);
		}
	}

	protected function _getMappedShippingMethod($shippingCode)
	{
		try{
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
		}catch(Exception $e){
			throw new Mage_Core_Exception($e->getMessage());
		}

	}

	protected function _getAddressInfo($addressObj)
	{

		return array(
			'first_name' => $addressObj->firstname,
			'last_name' => $addressObj->lastname,
			'region_id' => $addressObj->getRegionId(),
			'region' => $addressObj->getRegion(),
			'street' => $addressObj->getStreet(),
			'city' => $addressObj->getCity(),
			'postcode' => $addressObj->getPostCode(),
			'email' => $addressObj->getEmail(),
			'telephone' => $addressObj->getTelephone()
		);
	}

	protected function _getOrderItems($order)
	{
		$items = array();
		foreach($order->getAllVisibleItems() as $i){
			$items[] = array(
				'product_id' => $i->getProductId(),
				'sku' => $i->getSku(),
				'price' => $i->getPrice()
			);
		}
		return $items;
	}

	protected function _getTrackingNumbers($order)
	{
		$tracknumbers = array();
		$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
			->setOrderFilter($order)
			->load();
			foreach ($shipmentCollection as $shipment){
				// This will give me the shipment IncrementId, but not the actual tracking information.
				foreach($shipment->getAllTracks() as $tracknum)
				{

					$tracknums[]= array(
						'number' => $tracknum->getNumber(),
						'carrier' => $tracknum->getCarrierCode(),
					);
				}
			}
			return $tracknums;
	}

	protected function _getLocation($resource)
	{
		/** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
		$apiTypeRoute = Mage::getModel('api2/route_apiType');

		$chain = $apiTypeRoute->chain(new Zend_Controller_Router_Route('betterest/orders/:order_id'));
		$params = array(
		    'api_type' => $this->getRequest()->getApiType(),
		    'order_id' => $resource->getId()
		);
		$uri = $chain->assemble($params);

		return '/'.$uri;
	}

}