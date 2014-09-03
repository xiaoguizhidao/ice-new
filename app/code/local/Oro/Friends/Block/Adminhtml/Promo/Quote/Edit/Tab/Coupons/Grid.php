<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */ 
class Oro_Friends_Block_Adminhtml_Promo_Quote_Edit_Tab_Coupons_Grid
    extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid
{

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($item)
    {
        $params = array('id' => $item->getId());

        $rule = Mage::registry('current_promo_quote_rule');
        if ($rule) {
            $params['rule'] = $rule->getId();
        }

        return $this->getUrl('*/oro_friends_promo_coupon/edit', $params);
    }

}
