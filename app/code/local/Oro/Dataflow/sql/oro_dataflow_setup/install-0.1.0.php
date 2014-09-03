<?php
/**
 * Create table 'oro_dataflow/schedule_import'
 */
$table = $this->getConnection()
    ->newTable($this->getTable('oro_dataflow/schedule_import'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
        'identity'  => true,
        'unsigned'  => true,
        'primary'   => true,
    ), 'Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
    ), 'User Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'default'   => 'CURRENT_TIMESTAMP',
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'default'   => '0000-00-00 00:00:00',
    ), 'Updated At')
    ->addColumn('scheduled_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'default'   => '0000-00-00 00:00:00',
    ), 'Scheduled At')
    ->addColumn('executed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'nullable'  => true,
    ), 'Executed At')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, '32', array(
        'default'   => '',
    ), 'Status')
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
    ), 'Profile Id')
    ->addColumn('rows_complete', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => true,
    ), 'Rows Complete')
    ->addColumn('rows_total', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => true,
    ), 'Rows Total')
    ->addColumn('progress', Varien_Db_Ddl_Table::TYPE_FLOAT, NULL, array(
        'nullable'  => true,
    ), 'Progress')
    ->addColumn('ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
    ), 'Ids')
    ->addColumn('batch_size', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
        'default'   => '250',
    ), 'Batch Size')
    ->addColumn('batch_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => true,
    ), 'Batch Id')
    ->addColumn('file_path', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable'  => true,
    ), 'File Path')
    ->addColumn('log', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
    ), 'Log')
    ->setComment('Oro Dataflow Schedule Import');
$this->getConnection()->createTable($table);

/**
 * Create table 'oro_dataflow/schedule_export'
 */
$table = $this->getConnection()
    ->newTable($this->getTable('oro_dataflow/schedule_export'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
        'identity'  => true,
        'unsigned'  => true,
        'primary'   => true,
    ), 'Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
    ), 'User Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'default'   => 'CURRENT_TIMESTAMP',
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'default'   => '0000-00-00 00:00:00',
    ), 'Updated At')
    ->addColumn('scheduled_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'default'   => '0000-00-00 00:00:00',
    ), 'Scheduled At')
    ->addColumn('executed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, NULL, array(
        'nullable'  => true,
    ), 'Executed At')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, '32', array(
        'default'   => '',
    ), 'Status')
    ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
    ), 'Profile Id')
    ->addColumn('rows_complete', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => true,
    ), 'Rows Complete')
    ->addColumn('rows_total', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => true,
    ), 'Rows Total')
    ->addColumn('progress', Varien_Db_Ddl_Table::TYPE_FLOAT, NULL, array(
        'nullable'  => true,
    ), 'Progress')
    ->addColumn('ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
    ), 'Ids')
    ->addColumn('batch_size', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => false,
        'default'   => '250',
    ), 'Batch Size')
    ->addColumn('batch_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
        'nullable'  => true,
    ), 'Batch Id')
    ->addColumn('file_path', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable'  => true,
    ), 'File Path')
    ->addColumn('log', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
    ), 'Log')
    ->setComment('Oro Dataflow Schedule Export');
$this->getConnection()->createTable($table);
