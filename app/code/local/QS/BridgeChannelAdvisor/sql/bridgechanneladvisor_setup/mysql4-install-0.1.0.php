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

DROP TABLE IF EXISTS {$this->getTable('channel_categories')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_categories')}(
  `category_id` int(10) unsigned NOT NULL auto_increment ,
  `category_name` varchar(50) NOT NULL ,
  `uploaded_at` timestamp NOT NULL,
  PRIMARY KEY (`category_id`),
  INDEX (`category_id`)
)  ENGINE=INNODB CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('channel_attributes')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_attributes')}(
  `attribute_id` int(10) unsigned NOT NULL auto_increment ,
  `attribute_name` varchar(50) NOT NULL ,
  `uploaded_at` timestamp NOT NULL,
  PRIMARY KEY (`attribute_id`),
  INDEX (`attribute_id`)
)  ENGINE=INNODB CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('channel_relations')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_relations')}(
  `relation_id` int(10) unsigned NOT NULL auto_increment ,
  `category_id` int(10) NOT NULL ,
  `attribute_id` int(10) NOT NULL,
  PRIMARY KEY (`relation_id`),
  INDEX (`relation_id`),
  CONSTRAINT `FK_category_rule` FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('channel_categories')} (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>