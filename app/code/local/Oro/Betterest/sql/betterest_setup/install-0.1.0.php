<?php
/**
 * @category   Oro
 * @package    Oro_Betterest
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
$table = $this->getConnection()
    ->newTable($this->getTable('oro_betterest_shipping_map'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
        'identity'  => true,
        'unsigned'  => true,
        'primary'   => true,
    ), 'Id')
    ->addColumn('internal_shipping_method', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Shipping Method')
    ->addColumn('external_shipping_method', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Shipping Method')
    ->setComment('Oro Betterest Shipping Mapper');
$this->getConnection()->createTable($table);