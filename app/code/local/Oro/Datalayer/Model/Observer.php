<?php

/**
 * @category     Oro
 * @package        Oro_Datalayer
 * @copyright    Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Datalayer_Model_Observer
{
    private $_productHelper;
    private $_pageData = array();
    private $_dataLayer;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->setDataLayer(Mage::getSingleton('datalayer/datalayer'));
        $this->setHelper(Mage::helper('datalayer/data'));
        $this->setSession(Mage::getModel('core/session'));
    }

    /**
     * Sets the dataLayer with information that is set on every page.
     * This includes cart,store and user information. It also checks for
     * coupon and newsletter submissions
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function globalDataHook(Varien_Event_Observer $observer)
    {
        $helper  = $this->getHelper();
        $dl      = $this->getDataLayer();
        $session = $this->getSession();

        $dl->setData('cart', (object)$helper->getCartData());
        $dl->setData('user', (object)$helper->getUserData());
        $dl->setData('store', (object)$helper->getStoreData());
        $tempData = $dl->getData('tempData');

        if (!$tempData) {
            $tempData = array();
        }

        $newsletterSignup      = $helper->getSuccessfulNewsletterSignup();
        $newsletterSignupError = $helper->getNewsletterSignupError();

        if ($newsletterSignup) {
            $tempData['newsletter']['success'] = $newsletterSignup;

        } else if ($newsletterSignupError) {
            $tempData['newsletter']['error'] = $newsletterSignupError;
        }

        $couponCodeSubmitted = $session->getCouponCodeSubmitted();
        if ($couponCodeSubmitted) {

            $tempData['coupon']['code'] = $couponCodeSubmitted;

            $session->unsCouponCodeSubmitted();

            $couponCodeStatus             = $session->getCouponCodeStatus();
            $tempData['coupon']['status'] = $couponCodeStatus;

            if ($couponCodeStatus) {
                $session->unsCouponCodeStatus();
            }

        }

        $dl->setData('tempData', $tempData);

    }

    /**
     * sets the homepage pageType
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function homePageHook(Varien_Event_Observer $observer)
    {
        $this->getDataLayer()->setData('pageType', 'homepage');
    }


    /**
     * sets the CMS page information
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function cmsPageHook(Varien_Event_Observer $observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        $pageId     = $controller->getRequest()->getParam('page_id');

        if ($pageId) {
            $page = Mage::getModel('cms/page')->load($pageId);

            $pageData = array(
                'id'           => $pageId,
                'title'        => $page->getTitle(),
                'metaKeywords' => $page->getMetaKeywords(),
                'slug'         => $page->getIdentifier()
            );
            $this->getDataLayer()->setData('pageData', (object)$pageData);
        }
    }

    /**
     * sets product information
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function productPageHook(Varien_Event_Observer $observer)
    {

        $controller    = $observer->getEvent()->getControllerAction();
        $productId     = (int)$controller->getRequest()->getParam('id');
        $product       = Mage::getModel('catalog/product')->load($productId);
        $iceHelper     = Mage::helper('ice');
        $productRibbon = $iceHelper->getRibbon($product);
        $pageData      = array(
            'productId'      => $productId,
            'id'             => $product->getId(),
            'entityId'       => $product->getEntityId(),
            'name'           => $product->getName(),
            'sku'            => $product->getSku(),
            'isConfigurable' => $product->isConfigurable(),
            'isNew'          => ($productRibbon == 'new') ? true : false,
            'isOnClearance'  => ($productRibbon == 'clearance') ? true : false,
            'isOnSale'       => ($iceHelper->isSaleRibbon($product) == 'sale') ? true : false,
            'isSaleable'     => $product->isSaleable(),
            'inventory'      => round(Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty()),
            'urls'           => array(
                'url'        => $product->getUrlPath(),
                'urlInStore' => $product->getUrlInStore(),
                'productUrl' => $product->getProductUrl(),
            ),
            'status'         => $product->getStatus(),
            'prices'         => array(
                'regular' => $product->getPrice(),
                'final'   => $product->getFinalPrice(),
            ),
            'images'         => array(
                'image'    => $product->getImageUrl(),
                'smallImg' => $product->getSmallImageUrl(),
                'thumbImg' => $product->getThumbnailUrl(),
            ),
            'attributes'     => $this->getHelper()->getProductAdditionalData($product),
            'options'        => $this->getHelper()->getProductOptions($product)
        );

        foreach ($product->getCategoryCollection()->addAttributeToSelect('name')->getItems() as $cat) {
            $loadedCat                = $cat->load($cat->getId());
            $pageData['categories'][] = array(
                'name' => $loadedCat->getName(),
                'id'   => $loadedCat->getId(),
                'url'  => $loadedCat->getUrl()
            );

        }
        $dl = $this->getDataLayer();
        $dl->setData('pageType', 'product');
        $dl->setData('pageData', (object)$pageData);
    }


    /**
     * sets category information including filters selected
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function categoryPageHook(Varien_Event_Observer $observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        $catId      = (int)$controller->getRequest()->getParam('id');
        $cat        = Mage::getModel('catalog/category')->load($catId);

        $pageData = array();

        if ($cat->getId()) {
            $filterObj = Mage::getModel('catalog/layer');
            $filterObj->setCurrentCategory($cat);

            $filters = $this->getHelper()->getSelectedFilters($filterObj);

            $pageData = array(
                'id'          => $catId,
                'breadCrumbs' => $this->getHelper()->getBreadcrumbsForCategory($cat),
                'name'        => $cat->getName(),
                'filters'     => $filters
            );

        }
        $dl = $this->getDataLayer();
        $dl->setData('pageType', 'category');
        $dl->setData('pageData', (object)$pageData);
    }


    /**
     * sets the search term and filters selected (if any) when a user searches
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function searchPageHook(Varien_Event_Observer $observer)
    {

        $controller = $observer->getEvent()->getControllerAction();
        $filterObj  = Mage::getSingleton('catalogsearch/layer');
        $filters    = $this->getHelper()->getSelectedFilters($filterObj);
        $dl         = $this->getDataLayer();
        $dl->setData('pageType', 'search');
        $dl->setData('pageData', (object)array(
            'searchTerm' => $controller->getRequest()->getParam('q'),
            'filters'    => $filters
        ));

    }

    /**
     * sets the dataLayer with the options chosen when doing an advanced search
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function advancedSearchPageHook(Varien_Event_Observer $observer)
    {

        $controller = $observer->getEvent()->getControllerAction();
        $q          = Mage::getSingleton('catalogsearch/advanced');
        $dl         = $this->getDataLayer();
        $dl->setData('pageType', 'advancedSearch');
        $dl->setData('pageData', (object)array(
            'searchCriteria' => $this->getHelper()->getSearchCriteria(),
            'productCount'   => ($q->getNumResults()) ? $q->getNumResults() : 0
        ));

    }

    /**
     * sets a friendly pageType for the checkout page
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function checkoutOnepageHook(Varien_Event_Observer $observer)
    {
        $this->getDataLayer()->setData('pageType', 'checkout');
    }

    /**
     * sets two session values relating to the coupon
     * it sets the submitted coupon value and the status of the coupon (valid|invalid)
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function couponSubmitHook(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();

        $couponCode = (string)$request->getParam('coupon_code');
        $quote      = Mage::getSingleton('checkout/session')->getQuote();
        $session    = $this->getSession();
        $session->setCouponCodeSubmitted($couponCode);

        if (!strlen($quote->getCouponCode())) {
            $session->setCouponCodeStatus('invalid');
        } else {
            $session->setCouponCodeStatus('valid');
        }
    }

    /**
     * sets a session value of the removed product data
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function cartRemoveHook(Varien_Event_Observer $observer)
    {
        $cartItemId = Mage::app()->getRequest()->getParam('id', 0);
        $product    = Mage::getModel('sales/quote_item')->load($cartItemId);
        if ($product->getId()) {
            $this->getSession()->setProductRemovedFromShoppingCart(
                new Varien_Object(array(
                    'id'        => $product->getId(),
                    'productId' => $product->getProductId(),
                    'entityId'  => $product->getEntityId(),
                    'name'      => $product->getName(),
                    'price'     => $product->getPrice(),
                ))
            );
        }
    }


    /**
     * sets a session value of the newly added product data
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function cartAddHook(Varien_Event_Observer $observer)
    {
        $product = Mage::getModel('catalog/product')
            ->load(Mage::app()->getRequest()->getParam('product', 0));
        $session = $this->getSession();
        if ($product->getId()) {
            $categories = $product->getCategoryIds();
            $session->setProductToShoppingCart(
                new Varien_Object(array(
                    'id'       => $product->getId(),
                    'entityId' => $product->getEntityId(),
                    'qty'      => Mage::app()->getRequest()->getParam('qty', 1),
                    'name'     => $product->getName(),
                    'price'    => $product->getPrice(),
                ))
            );

            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if ($quote->getItemsSummaryQty() == 0) {
                $session->setCartIsNew(true);
            }
        }
    }


    /**
     * sets the dataLayer pageType value to a more friendly name
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function cartPageHook(Varien_Event_Observer $observer)
    {
        $this->getDataLayer()->setData('pageType', 'cart');
    }


    /**
     * Sets order information to the dataLayer
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function orderCompleteHook(Varien_Event_Observer $observer)
    {
        $checkoutSession = Mage::getSingleton('checkout/session');
        $orderId         = $checkoutSession->getLastRealOrderId();
        $orderData       = array('orderId' => $orderId);

        if ($orderId) {
            $order           = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $payment         = $order->getPayment();
            $shipping        = $order->getShippingAddress();
            $billing         = $order->getBillingAddress();
            $tax             = $order->getFullTaxInfo();
            $currency        = $order->getOrderCurrency();
            $shippingCarrier = $order->GetShippingCarrier();

            $orderData['shipping'] = array(
                'email'   => $shipping->getEmail(),
                'name'    => array(
                    'first'    => $shipping->getData('firstname'),
                    'last'     => $shipping->getData('lastname'),
                    'fullName' => $shipping->getName()
                ),
                'address' => array(
                    'street'     => $shipping->getStreetFull(),
                    'city'       => $shipping->getCity(),
                    'postalCode' => $shipping->getData('postcode'),
                    'country'    => $shipping->getCountry(),
                    'region'     => array(
                        'id'     => $shipping->getRegionId(),
                        'code'   => $shipping->getRegionCode(),
                        'region' => $shipping->getregion()
                    ),
                    'phone'      => $shipping->getTelephone()
                )
            );
            $orderData['billing']  = array(
                'name'    => array(
                    'first'    => $billing->getData('firstname'),
                    'last'     => $billing->getData('lastname'),
                    'fullName' => $billing->getName()
                ),
                'address' => array(
                    'street'     => $billing->getStreetFull(),
                    'region'     => array(
                        'id'     => $billing->getRegionId(),
                        'code'   => $billing->getRegionCode(),
                        'region' => $billing->getRegion()
                    ),
                    'city'       => $billing->getCity(),
                    'postalCode' => $billing->getData('postcode'),
                ),
            );

            $orderData['order'] = array(
                'orderId'     => $orderId,
                'total'       => round($order->grand_total, 2),
                'subtotal'    => round($order->subtotal, 2),
                'paymentType' => $payment->getData('method'),
                'tax'         => array(
                    'amount'  => 0,
                    'percent' => 0
                ),
                'currency'    => $currency->getCurrencyCode(),
                'shipping'    => array(
                    'method'      => $order->getShippingMethod(),
                    'carrierCode' => $order->getShippingCarrier()->getCarrierCode(),
                    'description' => $order->shipping_description,
                    'amount'      => round($order->getShippingAmount(), 2),
                )
            );

            if (is_array($tax) && !empty($tax)) {
                $orderData['order']['tax']['amount']  = $tax[0]['amount'];
                $orderData['order']['tax']['percent'] = $tax[0]['percent'];
            }
            $couponCode = $order->getCouponCode();
            if ($couponCode) {
                $orderData['order']['coupon'] = array(
                    'code'     => $couponCode,
                    'discount' => $order->getDiscountAmount()
                );
            }
            $products = array();
            foreach ($order->getAllVisibleItems() as $p) {
                $prod       = $p->getProduct();
                $products[] = array(
                    'id'         => $p->getId(),
                    'price'      => $p->getPrice(),
                    'qty'        => $p->getQtyToShip(),
                    'productId'  => $p->getProductId(),
                    'entityId'   => $prod->getEntityId(),
                    'sku'        => $p->getSku(),
                    'name'       => $p->getName(),
                    'taxAmt'     => $p->getTaxAmount(),
                    'urls'       => array(
                        'url'        => $prod->getUrlPath(),
                        'urlInStore' => $prod->getUrlInStore(),
                        'productUrl' => $prod->getProductUrl()
                    ),
                    'status'     => $prod->getStatus(),
                    'prodPrice'  => $prod->getPrice(),
                    'finalPrice' => $prod->getFinalPrice(),
                    'images'     => array(
                        'image'    => $prod->getImage(),
                        'smallImg' => $prod->getSmallImageUrl(),
                        'thumbImg' => $prod->getThumbnailUrl()
                    ),
                );
            }
            $orderData['products'] = $products;
        }

        $dl = $this->getDataLayer();
        $dl->setData('pageType', 'orderComplete');
        $dl->setData('orderData', $orderData);
    }


    /**
     * Catches and sets an error to a session when a user does not succesfully
     * sign up for the newsletter
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */

    public function newsletterSignupHook(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();
        if ($request->isPost() && $request->getPost('email')) {
            $session         = $this->getSession();
            $customerSession = Mage::getSingleton('customer/session');
            $email           = (string)$request->getPost('email');
            $error           = '';
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $error = 'inavlid';
                }

                $ownerId = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    $error = 'duplicate';
                }
            } catch (Mage_Core_Exception $e) {
                $error = $e->getMessage();
            } catch (Exception $e) {
                $error = 'unknown';
            }
            if ($error) {
                $session->setNewsletterSignupError($error);
            }
        }

    }

    /**
     * sets a session value for when user successfully signs up for the newsletter
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function newsletterSuccessfulSignupHook(Varien_Event_Observer $observer)
    {
        $event      = $observer->getEvent();
        $subscriber = $event->getDataObject();
        $data       = $subscriber->getData();

        $statusChange = $subscriber->getIsStatusChanged();

        if ($data['subscriber_status'] == "1" && $statusChange == true) {
            $this->getSession()->setSuccessfulNewsletterSignup(true);
        }

    }


    /**
     * @param Oro_Datalayer_Model_Datalayer $dataLayer
     * @return void
     */
    public function setDataLayer($dataLayer)
    {
        $this->_dataLayer = $dataLayer;
    }

    /**
     * @return Oro_DataLayer_Model_DataLayer
     */

    public function getDataLayer()
    {
        return $this->_dataLayer;
    }


    /**
     * @param Oro_Datalayer_Helper_Data @helper
     * @return void
     */

    public function setHelper($helper)
    {
        $this->_helper = $helper;
    }


    /**
     * @return Oro_Datalayer_Helper_Data
     */

    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @param Mage_Core_Model_Session $session
     * @return void
     */

    public function setSession($session)
    {
        $this->_session = $session;
    }

    /**
     * @return Mage_Core_Model_Session
     */

    public function getSession()
    {
        return $this->_session;
    }

}
