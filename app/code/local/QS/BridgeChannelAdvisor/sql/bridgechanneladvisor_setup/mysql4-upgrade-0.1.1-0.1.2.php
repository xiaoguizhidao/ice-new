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

DROP TABLE IF EXISTS {$this->getTable('channel_types')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_types')}(
  `type_id` int(10) unsigned NOT NULL auto_increment ,
  `attribute_set_id` smallint(5) NOT NULL ,
  `category_id` int(10) NOT NULL,
  PRIMARY KEY (`type_id`),
  INDEX (`type_id`),
  CONSTRAINT `FK_attribute_set_rule` FOREIGN KEY (`attribute_set_id`) REFERENCES {$this->getTable('eav_attribute_set')} (`attribute_set_id`) ON DELETE CASCADE
)  ENGINE=INNODB CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('channel_items')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_items')}(
  `item_id` int(10) unsigned NOT NULL auto_increment ,
  `type_id` int(10) NOT NULL ,
  `product_id` int(10) NOT NULL ,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`item_id`),
  INDEX (`item_id`),
  CONSTRAINT `FK_product_rule` FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>