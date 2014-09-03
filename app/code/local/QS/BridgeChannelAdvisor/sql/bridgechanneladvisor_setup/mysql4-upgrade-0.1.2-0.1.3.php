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

DROP TABLE IF EXISTS {$this->getTable('channel_pair_attributes')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_pair_attributes')}(
  `pair_id` int(10) unsigned NOT NULL auto_increment ,
  `mage_attribute_id` int(10) NOT NULL ,
  `ca_attribute_id` int(10) NOT NULL ,
  `type_id` int(10) NOT NULL,
  PRIMARY KEY (`pair_id`),
  INDEX (`pair_id`),
  CONSTRAINT `FK_mage_attribute_rule` FOREIGN KEY (`mage_attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>