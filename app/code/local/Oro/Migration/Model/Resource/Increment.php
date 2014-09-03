<?php
/**
 * @category   Oro
 * @package    Oro_Migration
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Migration_Model_Resource_Increment extends Mage_Core_Model_Resource_Db_Abstract
{

    protected $_remoteDb = null;



    /**
     * Defines Resource Table and Unique Fields
     */
    public function _construct()
    {
        $this->_init('oro_migration/increment', null);
    }

    /**
     * Phase 1. Adds constant to tables autoincrement
     *
     * @param array $table
     */
    public function increment($table)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($table['table_name'], array('last_increment' => "MAX({$table['increment_field']})"));

        $lastId = (int) $this->_getWriteAdapter()->fetchOne($select);
        $newId = $lastId + $table['increment_value'];

        $this->_getWriteAdapter()->query("Alter table {$table['table_name']} AUTO_INCREMENT= {$newId}");

        $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), array(
            'table_name' => $table['table_name'],
            'last_increment' => $lastId,
            'increment_field' => $table['increment_field'],
        ));
    }

    /**
     * Fetches last autoincrement id of table
     *
     * @param string $table
     * @param string $filed
     * @return int
     */
    public function getTableLastId($table, $filed)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($table, array('last_increment' => "MAX({$filed})"));

        return (int) $this->_getWriteAdapter()->fetchOne($select);
    }

    /**
     * Fetches stored tables increments
     *
     * @return array
     */
    public function loadIncrements()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable());

        return $this->_getReadAdapter()->fetchAssoc($select);
    }

    /**
     * Gets Stored increments with last ids
     *
     * @return array
     */
    public function getIncrements()
    {
        $increments = $this->loadIncrements();

        foreach ($increments as &$increment) {
            $increment['last_id'] = $this->getTableLastId($increment['table_name'], $increment['increment_field']);
        }

        return $increments;
    }

    /**
     * Phase 2. Copies missing rows from remote DB to current
     *
     * @param $data
     * @return int
     */
    public function migrate($data)
    {
        $remote = $this->_getRemoteConnection($data);

        $select = $remote->select()
            ->from($data['table_name'])
            ->where($data['increment_field'] . ' > ?', $data['increment_value']);

        $remoteData = $remote->fetchAll($select);

        $result = 0;
        if (count($remoteData)) {
            $this->_getWriteAdapter()->startSetup();

            $moveData = array();
            if ($data['move']) {
                $select = $this->_getWriteAdapter()->select()
                    ->from($data['table_name'])
                    ->where($data['increment_field'] . ' > ?', $data['increment_value']);

                foreach ($this->_getWriteAdapter()->fetchAll($select) as $row) {
                    unset($row[$data['increment_field']]);
                    $moveData[] = $row;
                }

                $query = $this->_getWriteAdapter()->deleteFromSelect($select, $data['table_name']);
                $this->_getWriteAdapter()->exec($query);
            }

            $result = $this->_getWriteAdapter()->insertMultiple($data['table_name'], $remoteData);

            if ($moveData) {
                $result += $this->_getWriteAdapter()->insertMultiple($data['table_name'], $moveData);
            }

            $this->_getWriteAdapter()->update($this->getMainTable(),
                array(
                    'last_increment' => $data['increment_value'] + $result
                ),
                array('table_name' => $data['table_name'])
            );

            $this->_getWriteAdapter()->endSetup();
        }

        return $result;
    }

    /**
     * Phase 3. updates all rows from remote DB
     *
     * @param $data
     * @return int
     */
    public function update($data)
    {
        $sourceTbale = $data['source_db'].'.'.$data['table_name'];
        $targetTbale = $data['target_db'].'.'.$data['table_name'];

        $fields = array();

        foreach (array_keys($this->_getWriteAdapter()->describeTable($sourceTbale)) as $field) {
            $fields[] = 't.'. $field . ' = s.' . $field;
        }

        $fields = implode(', ', $fields);

        $sql = "
            update {$sourceTbale} as s, {$targetTbale} as t
            set {$fields}
            where s.{$data['increment_field']} = t.{$data['increment_field']}
        ";

        $this->_getWriteAdapter()->startSetup();

        $result = $this->_getWriteAdapter()->exec($sql);

        $this->_getWriteAdapter()->endSetup();

        return $result;
    }

    /**
     * Flushes increments
     */
    public function flush()
    {
        $this->_getWriteAdapter()->truncateTable($this->getMainTable());
    }

    /**
     * Estabilish connection with remote DB
     *
     * @param array $data
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getRemoteConnection($data)
    {
        if (!$this->_remoteDb) {
            $this->_remoteDb = Mage::getSingleton('core/resource_type_db_pdo_mysql')->getConnection(array(
                'host' => $data['db_host'],
                'port' => $data['db_port']? $data['db_port'] : 3306,
                'dbname' => $data['db_name'],
                'username' => $data['db_user'],
                'password' => $data['db_pass'],
                'initStatements' => "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'",
            ));
        }

        return $this->_remoteDb;
    }

    public function syncCoupons($data)
    {
        $remote = $this->_getRemoteConnection($data);

        $select = $remote->select()
            ->from(array('r' => $remote->getTableName('salesrule')), array())
            ->where('r.name like "rw%"')
            ->joinLeft(
                array('c' => $remote->getTableName('salesrule_coupon')),
                'r.rule_id = c.rule_id',
                array('code' => 'c.code', 'times_used' => 'c.times_used'));

        $remoteData = $remote->fetchAll($select);

        $result = 0;
        if (count($remoteData)) {
            $this->_getWriteAdapter()->startSetup();

            foreach ($remoteData as $usage) {
                $result += $this->_getWriteAdapter()->update(
                    $remote->getTableName('salesrule_coupon'),
                    array('times_used' => $usage['times_used']),
                    array('code = ?' => $usage['code'])
                );
            }

            $this->_getWriteAdapter()->endSetup();
        }

        return "Updated $result rows";
    }

    /**
     * Increment LastIDs
     *
     * @param int $value
     * @return array
     */
    public function incrementIDs($value)
    {
        $res = $this->_getWriteAdapter()->query("
            update {$this->_getWriteAdapter()->getTableName('eav_entity_store')}
            set increment_last_id = increment_last_id + {$value}
        ");

        if ($res) {
            return $this->getLastIds();
        }

        return array();
    }

    /**
     * Fetches Last Increment IDs for entities
     *
     * @return array
     */
    public function getLastIds()
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(
                array('ids' => $this->_getWriteAdapter()->getTableName('eav_entity_store')),
                array('increment' => 'ids.increment_last_id', 'store' => 'ids.store_id'))
            ->join(array('t' => $this->_getWriteAdapter()->getTableName('eav_entity_type')),
                'ids.entity_type_id = t.entity_type_id',
                array('name' => 't.entity_type_code')
            );

        return $this->_getWriteAdapter()->fetchAll($select);
    }

}
