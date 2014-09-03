<?php
/**
 * @category   Oro
 * @package    Oro_Ensighten
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Ensighten_Block_Json
    extends Mage_Core_Block_Abstract
{
    const XML_PATH_BOOTSTRAP_URI = 'oro_ensighten/general/bootstrap_uri';

    /**
     * @return string
     */
    public function getJson()
    {
        try {
            switch (true) {
                case Mage::registry('current_product'):
                    $data = $this->_getProductData();
                    break;
                case Mage::registry('current_category'):
                    $data = $this->_getCategoryData();
                    break;
                case preg_match('/^\\/{0,1}checkout\\/onepage\\/success\\/{0,1}/ui', $this->getRequest()->getPathInfo()):
                    $data = $this->_getOrderData();
                    break;
                case preg_match('/^\\/{0,1}checkout\\/{0,1}/ui', $this->getRequest()->getPathInfo()):
                    $data = $this->_getCartData();
                    break;
                default:
                    $data = array();
                    break;
            }
        } catch (Exception $e) {
            $data = array();
        }

        return Mage::helper('core')->jsonEncode($data);
    }

    /**
    * @return array
    */
    protected function _getProductData()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::registry('current_product');

        if (!$product) {
            Mage::throwException(Mage::helper('oro_ensighten')->__('Product not found.'));
        }

        $result = array(
            'data' => array(),
        );

        foreach ($product->getData() as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $result['data'][$key] = $value;
            }
        }

        $result['item'] = $result['data'];

        return $result;
    }

    /**
     * @return array
     */
    protected function _getCategoryData()
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::registry('current_category');

        if (!$category) {
            Mage::throwException(Mage::helper('oro_ensighten')->__('Category not found.'));
        }

        $result = array(
            'data' => $category->getData(),
        );

        $result['category'] = $result['data'];

        return $result;
    }

    /**
     * @return array
     */
    protected function _getCartData()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        $result = array(
            'data' => array(
                'revenue' => $quote->getGrandTotal()
            )
        );


        $result['cart'] = $result['data'];

        return $result;
    }

    /**
     * @return array
     */
    protected function _getOrderData()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order->getId()) {
            Mage::throwException(Mage::helper('oro_ensighten')->__('Order not found.'));
        }


        $result = array(
            'data' => array(
                'revenue' => $order->getGrandTotal(),
                'email' => $order->getCustomerEmail(),
                'orderId' => $order->getId(),
            )
        );

        $result['order'] = $result['data'];

        return $result;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html  = '<script type="text/javascript"> window.iceJson = ' . $this->getJson() . '; </script>';
        $html .= '<script type="text/javascript" src="' . Mage::getStoreConfig(self::XML_PATH_BOOTSTRAP_URI)
              . '"></script>';

        return $html;
    }
}
