<?php

/**
 * @category   Oro
 * @package    Oro_Datalayer
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Datalayer_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $_request = false;
    protected $_attrGroups = array();

    /**
     * @return boolean
     */
    public function getIsInternal()
    {
        $userIp      = Mage::helper('core/http')->getRemoteAddr();
        $internalIps = Mage::getStoreConfig('datalayer/settings/internal_ip');

        return Mage::helper('datalayer/ip')->ipInRange($userIp, $internalIps);
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        $customerHelper = Mage::helper('customer');
        $customer       = Mage::getSingleton('customer/session')->getCustomer();
        $userData       = array(
            'isInternal' => $this->getIsInternal(),
            'isLoggedIn' => $customerHelper->isLoggedIn(),
            'name'       => array(
                'first' => $customer->firstname,
                'last'  => $customer->lastname,
            ),
            'userId'     => $customer->getId(),
            'email'      => $customer->getEmail(),
            'ip'         => Mage::helper('core/http')->getRemoteAddr(),
        );

        if ($customer->getEmail()) {
            $newsLetter                         = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
            $userData['isNewsletterSubscriber'] = $newsLetter->isSubscribed();
        } else {
            $userData['isNewsletterSubscriber'] = false;
        }

        return $userData;
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        $request    = $this->getRequest();
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        return $module . ':' . $controller . ':' . $action;
    }

    /**
     * @return array
     */
    public function getCartData()
    {
        $quote   = Mage::getSingleton('checkout/session')->getQuote();
        $session = Mage::getModel('core/session');
        $cart    = array(
            'products'   => array(),
            'subTotal'   => 0,
            'grandTotal' => 0,
            'tax'        => 0,
            'totalQty'   => 0,
            'coupon'     => array()
        );

        if ($quote->hasItems()) {
            $products = array();
            foreach ($quote->getAllVisibleItems() as $p) {
                $products[] = $this->_prepareProductInfo($p);
            }

            $cart['products']   = $products;
            $totals             = $quote->getTotals();
            $cart['subTotal']   = $totals['subtotal']->getValue();
            $cart['grandTotal'] = $totals['grand_total']->getValue();
            $cart['tax']        = $totals['tax']->getValue();
            $cart['totalQty']   = $quote->getItemsSummaryQty();

            $productAdded = $session->getProductToShoppingCart();

            if ($productAdded) {
                $cart['productAdded'] = $productAdded->toArray();
                $session->unsProductToShoppingCart();
            }
        }

        $productRemoved = $session->getProductRemovedFromShoppingCart();

        if ($productRemoved) {
            $cart['productRemoved'] = $productRemoved->toArray();
            $session->unsProductRemovedFromShoppingCart();
        }

        $newCart = $session->getCartIsNew();
        if ($newCart) {
            $cart['isNew'] = $newCart;
            $session->unsCartIsNew();
        }

        $couponCode = $quote->getCouponCode();
        if ($couponCode) {
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
            $oRule   = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());

            $cart['coupon']['code']           = $couponCode;
            $cart['coupon']['discountAmount'] = $oRule->getDiscountAmount();
        }

        return $cart;

    }

    /**
     * @return array
     */
    public function getStoreData()
    {
        $store   = Mage::app()->getStore();
        $website = $store->getWebsite();
        return array(
            'name'    => $store->getFrontendName(),
            'store'   => array(
                'name' => $store->getName(),
                'id'   => $store->getStoreId(),
                'code' => $store->getCode()
            ),
            'website' => array(
                'name' => $website->getName(),
                'id'   => $store->getWebsiteId(),
                'code' => $website->getCode()
            )
        );
    }

    /**
     * @param Mage_Catalog_Model_Layer $filterObj
     * @return array
     */
    public function getSelectedFilters($filterObj)
    {
        $filterableAttributes = $filterObj->getFilterableAttributes();
        $params               = $this->getRequest()->getParams();

        $filterArr = array();
        if (is_object($filterableAttributes)) {
            foreach ($filterableAttributes->getItems() as $f) {
                $fName = $f->getName();

                if ($f->getName() && isset($params[$fName])) {
                    $fValue      = $params[$fName];
                    $filterArr[] = array(
                        'filter' => array(
                            'id'   => $f->getId(),
                            'code' => $fName,
                            'name' => $f->getFrontEndLabel()
                        ),
                        'value'  => array(
                            'name' => $f->getSource()->getOptionText($fValue),
                            'id'   => $fValue
                        )
                    );
                }
            }
        }

        return $filterArr;
    }

    /**
     * @return array
     */
    public function getSearchCriteria()
    {
        $q            = Mage::getSingleton('catalogsearch/advanced');
        $params       = $this->getRequest()->getParams();
        $searchedAttr = array();

        foreach ($q->getAttributes() as $attribute) {
            $value = $params[$attribute->getAttributeCode()];
            if ($value) {
                $name = $attribute->getStoreLabel();

                if (is_array($value)) {

                    if (isset($value['from']) && isset($value['to'])) {
                        $value = $this->_getRangeSearchCriteria($value);
                        if (!$value) {
                            continue;
                        }
                    }
                }
                if ($attribute->getFrontendInput() == 'select'
                    || $attribute->getFrontendInput() == 'multiselect'
                ) {
                    $value = $this->_getMultiSelectSearchCriteria($value, $attribute);

                } else if ($attribute->getFrontendInput() == 'boolean') {
                    $value = $value == 1
                        ? Mage::helper('catalogsearch')->__('Yes')
                        : Mage::helper('catalogsearch')->__('No');
                }

                $searchedAttr[] = array(
                    'name'  => $name,
                    'value' => $value,
                    'code'  => $attribute->getAttributeCode()
                );
            }
        }

        return $searchedAttr;
    }

    /**
     * @param array
     * @return array|boolean returns array if successful, returns false if values are empty
     */
    protected function _getRangeSearchCriteria($value)
    {
        $retValue = false;
        if (!empty($value['from']) || !empty($value['to'])) {
            if (isset($value['currency'])) {
                $currencyModel = Mage::getModel('directory/currency')->load($value['currency']);
                $from          = $currencyModel->format($value['from'], array(), false);
                $to            = $currencyModel->format($value['to'], array(), false);
            } else {
                $currencyModel = null;
            }

            if (strlen($value['from']) > 0 && strlen($value['to']) > 0) {
                // -
                $retValue = sprintf('%s - %s',
                    ($currencyModel ? $from : $value['from']), ($currencyModel ? $to : $value['to']));

            } elseif (strlen($value['from']) > 0) {
                // and more
                $retValue = Mage::helper('catalogsearch')->__('%s and greater', ($currencyModel ? $from : $value['from']));

            } elseif (strlen($value['to']) > 0) {
                // to
                $retValue = Mage::helper('catalogsearch')->__('up to %s', ($currencyModel ? $to : $value['to']));

            }
        }

        return $retValue;
    }

    /**
     * @param array $value
     * @param  Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return
     */
    public function _getMultiSelectSearchCriteria($value, $attribute)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $attribute->getSource()->getOptionText($val);

                if (is_array($value[$key])) {
                    $value[$key] = $value[$key]['label'];
                }
            }
            $value = implode(', ', $value);

        } else {

            $value = $attribute->getSource()->getOptionText($value);
            if (is_array($value)) {
                $value = $value['label'];
            }
        }

        return $value;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductOptions($product)
    {
        $optArr = array();

        if ($product->isConfigurable()) {

            $options = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

            foreach ($options as $item) {

                $prices = $item['values'];

                if (is_array($prices)) {

                    $opt = array('label' => $item['store_label'], 'code' => $item['attribute_code']);

                    foreach ($prices as $price) {

                        $opt['options'][] = array('label' => $price['label'], 'price' => $price['pricing_value'], 'id' => $price['value_index']);

                    }

                    $optArr[] = $opt;

                }
            }
        }

        return $optArr;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param array $excludeAttr
     * @return array
     */
    public function getProductAdditionalData($product, array $excludeAttr = array())
    {
        $data       = array();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {

            if ($attribute->getIsVisibleOnFront()
                && !in_array($attribute->getAttributeCode(), $excludeAttr)
            ) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())
                    || (string)trim($value) == '' || (string)trim($value == 'No')
                ) {
                    $value = '';
                } else if ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }
                if (is_string($value) && strlen($value)) {
                    $attr_group = $this->_getAttributeGroup($attribute->getAttributeGroupId());

                    $data[$attr_group->getAttributeGroupName()][] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode(),
                        'id'    => $attribute->getId(),
                    );
                }
            }
        }

        return $data;
    }


    /**
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getBreadcrumbsForCategory($category)
    {
        $crumbs = array();
        if ($category) {
            // get the categories in the order they are displayed in the store
            // $category->getParentCategories list the categories sorted by their IDs
            // this may not be the path that they are displayed.
            $categoryPath = explode(',', $category->getPathInStore());
            $parents      = $category->getParentCategories();

            foreach ($categoryPath as $catId) {
                if(isset($parents[$catId])){
                    $crumbs[] = array(
                        'name' => $parents[$catId]->getName(),
                        'id'   => $parents[$catId]->getId()
                    );
                }
            }
        }

        return array_reverse($crumbs);
    }


    /**
     * @return boolean
     */
    public function getSuccessfulNewsletterSignup()
    {
        $session = Mage::getModel('core/session');
        $signup  = $session->getSuccessfulNewsletterSignup();

        if ($signup) {
            $session->unsSuccessfulNewsletterSignup();
        }

        return $signup;
    }

    /**
     * @return string
     */
    public function getNewsletterSignupError()
    {
        $session = Mage::getModel('core/session');
        $error   = $session->getNewsletterSignupError();
        if ($error) {
            $session->unsNewsletterSignupError();
        }

        return $error;
    }


    /**
     *
     * @return Mage_Core_Controller_Request_Http
     */

    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = Mage::app()->getRequest();
        }

        return $this->_request;
    }

    /**
     * @param int $groupId
     * @return array
     */
    protected function _getAttributeGroup($groupId)
    {
        if (!isset($this->_attrGroups[$groupId])) {

            $this->_attrGroups[$groupId] = Mage::getModel('eav/entity_attribute_group')->load($groupId);
        }

        return $this->_attrGroups[$groupId];
    }

    /**
     * @param Mage_Sales_Model_Quote_Item
     * @return array
     */
    protected function _prepareProductInfo($quoteItem)
    {
        $prod        = $quoteItem->getProduct();
        $productData = array(
            'qty'       => $quoteItem->getQty(),
            'price'     => $quoteItem->getPrice(),
            'id'        => $quoteItem->getId(),
            'productId' => $quoteItem->getProductId(),
            'entityId'  => $prod->getEntityId(),
            'name'      => $quoteItem->getName(),
            'sku'       => $quoteItem->getSku(),

            'urls'      => array(
                'url'        => $prod->getUrlPath(),
                'urlInStore' => $prod->getUrlInStore(),
                'productUrl' => $prod->getProductUrl(),
            ),
            'status'    => $prod->getStatus(),
            'images'    => array(
                'image'    => $prod->getImageUrl(),
                'smallImg' => $prod->getSmallImageUrl(),
                'thumbImg' => $prod->getThumbnailUrl(),
            ),

        );

        return $productData;
    }
}
