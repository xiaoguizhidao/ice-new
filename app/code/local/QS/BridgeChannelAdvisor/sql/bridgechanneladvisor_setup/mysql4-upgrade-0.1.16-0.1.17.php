<?php
/**
 * ChannelAdvisor Install
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('channel_ship_types')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_ship_types')}(
  `ship_type_id` int(10) unsigned NOT NULL auto_increment ,
  `carrier_id` int(10) NOT NULL ,
  `mage_carrier_code` varchar(50) NOT NULL,
  PRIMARY KEY (`ship_type_id`),
  INDEX (`ship_type_id`)
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>