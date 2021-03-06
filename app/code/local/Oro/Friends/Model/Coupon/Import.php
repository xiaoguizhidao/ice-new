<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Model_Coupon_Import
 */
class Oro_Friends_Model_Coupon_Import
{
    /**
     * @param string $fileName
     * @param int $ruleId
     * @param bool $deleteExists
     * @param array $errors
     * @return int
     * @throws Exception
     */
    public function import($fileName, $ruleId, $deleteExists = false, &$errors = array())
    {
        $file = fopen($fileName, 'r');
        fgetcsv($file); // Skip headers
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection(Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE);
        $connection->beginTransaction();
        $table = $resource->getTableName('salesrule/coupon');
        $imported = 0;

        if (Mage::getModel('salesrule/rule')->load($ruleId)->getId()) {
            try {
                if ($deleteExists) {
                    $connection->delete($table, $connection->quoteInto('rule_id = ?', $ruleId, Zend_Db::INT_TYPE));
                }

                while (false !== ($data = fgetcsv($file))) {
                    $code = isset($data[0]) ? $data[0] : null;
                    $usesPerCoupon = isset($data[1]) ? $data[1] : 0;
                    $usesPerCustomer = isset($data[2]) ? $data[2] : 0;
                    $timesUsed = isset($data[3]) ? $data[3] : 0;

                    if ($connection->fetchOne($connection->select()->from($table, 'count(code)')->where('code = ?', $code))) {
                        $errors[] = Mage::helper('oro_friends/api')->__('Detected duplication for coupon code "%s"', $code);
                        continue;
                    }

                    if ($code) {
                        $connection->insert($table, array(
                            'rule_id' => $ruleId,
                            'code' => $code,
                            'usage_limit' => $usesPerCoupon,
                            'usage_per_customer' => $usesPerCustomer,
                            'times_used' => $timesUsed,
                            'type' => Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED
                        ));
                    }
                    $imported++;
                }
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        }

        return $imported;
    }
}
