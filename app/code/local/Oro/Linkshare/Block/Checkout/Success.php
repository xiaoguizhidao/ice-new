<?php
/**
 * @category   Oro
 * @package    Oro_Linkshare
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Linkshare_Block_Checkout_Success
    extends Mage_Core_Block_Abstract
{

    /**
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrder()
    {
        if (!$this->hasData('order')) {
            $_order = Mage::getModel('sales/order')->load($this->getLastOrderId());

            $this->setData('order', $_order);
        }

        return $this->getData('order');
    }

    /**
     * @return string
     */
    public function getLastOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastOrderId();
    }

    /**
     * @todo: Make it working :)
     *
     * @return float|int
     */
    public function getTaxRate()
    {
        //to deduct VAT, GST or any value added tax from reporting replace 0 with required rate - currently for US its 0 percent, for UK VAT is 20 percent and for AU GST is 10 percent
        $ls_taxrate = 0; //replace 0 with your current rate such as 0, 10 or 20

        if ($ls_taxrate == 0) {
            $ls_taxrate = 1;
        } else {
            $ls_taxrate = (($ls_taxrate + 100) / 100);
        }

        return $ls_taxrate;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        $data = $this->getTrackingInfo();

        $_helper = $this->helper('linkshare');
        $_order  = $this->getOrder();

        $result = '//track.linksynergy.com/ep?';
        $result .= 'mid=' . $_helper->getMid();
        $result .= '&ord=' . $_order->getIncrementId();
        $result .= '&cur=' . $_order->getOrderCurrencyCode();
        $result .= '&skulist=' . join('|', $data->getSkus());
        $result .= '&qlist=' . join('|', $data->getQtys());
        $result .= '&amtlist=' . join('|', $data->getAmounts());
        $result .= '&namelist=' . join('|', $data->getNames());

        return $result;
    }

    /**
     * @return Varien_Object
     */
    public function getTrackingInfo()
    {
        $skusArr   = array();
        $amountArr = array();
        $qtysArr   = array();
        $namesArr  = array();

        $_order = $this->getOrder();

        /**
         * @var Mage_Sales_Model_Order_Item $item
         */
        foreach ($_order->getAllVisibleItems() as $item) {
            $skusArr[]   = urlencode($item->getSku());
            $amountArr[] = round((($item->getPrice() * $item->getQtyOrdered()) / $this->getTaxRate()) * 100);
            $qtysArr[]   = round($item->getQtyOrdered());
            $namesArr[]  = urlencode($item->getName());
        }

        $_discountAmount = $_order->getDiscountAmount();
        if ($_discountAmount != 0) {
            $skusArr[]   = 'Discount';
            $amountArr[] = round(($_discountAmount / $this->getTaxRate()) * 100);
            $qtysArr[]   = '0';
            $namesArr[]  = 'Discount';
        }

        return new Varien_Object(array(
            'skus'    => $skusArr,
            'amounts' => $amountArr,
            'qtys'    => $qtysArr,
            'names'   => $namesArr
        ));
    }

    /**
     * @return string
     */
    protected function _isEnabled()
    {
        return (
            $this->_validateCouponPrefix() &&
            $this->_validateCookie()
        );
    }

    /**
     * @return bool
     */
    protected function _validateCouponRequired()
    {
        return (bool)mb_strlen($this->_getCouponCode());
    }

    /**
     * @param bool $default
     *
     * @return bool|null
     */
    protected function _validateCouponPrefix($default = true)
    {
        $needFilterByPrefix = $this->helper('linkshare')->getConfigFlag('coupon_filter_by_prefix');

        if ($needFilterByPrefix) {
            $_couponPrefix = $this->_getCouponPrefix();
            $_regExp       = '/^' . $_couponPrefix . '/i';

            if ($_couponPrefix) {
                return (bool)preg_match($_regExp, $this->_getCouponCode());
            }
        }

        return $default;
    }

    /**
     * @param bool $default
     *
     * @return bool|null
     */
    protected function _validateCookie($default = true)
    {
        $needFilterByCookie = $this->helper('linkshare')->getConfigFlag('cookie_required');
        if ($needFilterByCookie) {
            return (bool)$this->_getCookie();
        }

        return $default;
    }

    /**
     * @return string
     */
    protected function _getCouponCode()
    {
        return trim((string)$this->getOrder()->getCouponCode());
    }

    /**
     * @return string
     */
    protected function _getCouponPrefix()
    {
        return trim((string)$this->helper('linkshare')->getConfig('coupon_prefix'));
    }

    /**
     * @return mixed
     */
    protected function _getCookie()
    {
        $name = trim((string)$this->helper('linkshare')->getConfig('cookie_name'));

        return Mage::getModel('core/cookie')->get($name);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_isEnabled()?'<img src="'.$this->getImageUrl().'" alt="" />':'';
    }
}
