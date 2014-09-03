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

DROP TABLE IF EXISTS {$this->getTable('channel_ship')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_ship')}(
  `carrier_id` int(10) unsigned NOT NULL auto_increment,
  `ca_carrier_id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL,
  `carrier_name` varchar(50) NOT NULL,
  `carrier_code` varchar(50) NOT NULL,
  `class_code` varchar(50) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  PRIMARY KEY (`carrier_id`),
  INDEX (`carrier_id`)
  /* CONSTRAINT `FK_attribute_set_rule` FOREIGN KEY (`attribute_set_id`) REFERENCES {$this->getTable('eav_attribute_set')} (`attribute_set_id`) ON DELETE CASCADE */
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>