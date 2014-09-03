<?php
/**
 * @category   Oro
 * @package    Oro_Migration
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Configuration helper
 */
class Oro_Migration_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Gets initial tables list to increment
     *
     * @return array
     */
    public function getTablesToIncrement()
    {
        $tables = array(
            // orders
            array('name' => 'sales_flat_order', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_order_address', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_order_item', 'checked' => true, 'increment' => 'item_id'),
            array('name' => 'sales_flat_order_payment', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_order_status_history', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_order_tax', 'checked' => true, 'increment' => 'tax_id'),
            array('name' => 'sales_order_tax_item', 'checked' => true, 'increment' => 'tax_item_id'),
            array('name' => 'sales_flat_order_grid', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'am_ogrid_order_item', 'checked' => true, 'increment' => 'ogrid_item_id'),
            //quotes
            array('name' => 'sales_flat_quote', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_quote_address', 'checked' => true, 'increment' => 'address_id'),
            array('name' => 'sales_flat_quote_address_item', 'checked' => true, 'increment' => 'address_item_id'),
            array('name' => 'sales_flat_quote_item', 'checked' => true, 'increment' => 'item_id'),
            array('name' => 'sales_flat_quote_item_option', 'checked' => true, 'increment' => 'option_id'),
            array('name' => 'sales_flat_quote_payment', 'checked' => true, 'increment' => 'payment_id'),
            array('name' => 'sales_flat_quote_shipping_rate', 'checked' => true, 'increment' => 'rate_id'),
            //shipments
            array('name' => 'sales_flat_shipment', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_shipment_comment', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_shipment_item', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_shipment_track', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_shipment_grid', 'checked' => true, 'increment' => 'entity_id'),
            // payment
            array('name' => 'sales_payment_transaction', 'checked' => true, 'increment' => 'transaction_id'),
            // refunds
            array('name' => 'sales_flat_creditmemo', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_creditmemo_comment', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_creditmemo_item', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_creditmemo_grid', 'checked' => true, 'increment' => 'entity_id'),
            // invoices
            array('name' => 'sales_flat_invoice', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_invoice_comment', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_invoice_item', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'sales_flat_invoice_grid', 'checked' => true, 'increment' => 'entity_id'),
            // customers
            array('name' => 'customer_entity', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'customer_entity_datetime', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_entity_decimal', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_entity_int', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_entity_text', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_entity_varchar', 'checked' => true, 'increment' => 'value_id'),
            // customer addresses
            array('name' => 'customer_address_entity', 'checked' => true, 'increment' => 'entity_id'),
            array('name' => 'customer_address_entity_datetime', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_address_entity_decimal', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_address_entity_int', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_address_entity_text', 'checked' => true, 'increment' => 'value_id'),
            array('name' => 'customer_address_entity_varchar', 'checked' => true, 'increment' => 'value_id'),
        );

        $incremented = Mage::getResourceModel('oro_migration/increment')->getIncrements();

        foreach ($tables as &$table) {
            if (isset($incremented[$table['name']])) {
                $table['incremented'] = $incremented[$table['name']]['last_increment'];
                $table['last_id'] = $incremented[$table['name']]['last_id'];
                $table['checked'] = false;
            } else {
                $table['checked'] = true;
            }
            $table['checked2'] = $table['checked3'] = $table['move'] = false;
        }

        return $tables;
    }

    public function getRulesToSync()
    {
        $rules = array();

        $rulesCollection = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('name', array('like' => 'rw%'));

        /** @var Mage_SalesRule_Model_Rule $rule */
        foreach ($rulesCollection as $rule) {
            /**
             * @var Mage_SalesRule_Model_Resource_Coupon_Collection $collection
             */
            $coupons = Mage::getResourceModel('salesrule/coupon_collection')
                ->addRuleToFilter($rule)
                ->toArray();

            $rules[$rule->getId()] = array('name' => $rule->getName(), 'coupons' => $coupons['items']);
        }

        return $rules;
    }
}
