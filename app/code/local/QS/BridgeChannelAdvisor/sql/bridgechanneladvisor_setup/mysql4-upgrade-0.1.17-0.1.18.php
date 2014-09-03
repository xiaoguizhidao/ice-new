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

DROP TABLE IF EXISTS {$this->getTable('channel_email')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_email')}(
  `id` int(10) UNSIGNED NOT NULL auto_increment ,
  `process_id` int(10) NOT NULL ,
  `recording_time` timestamp NOT NULL ,
  `message` varchar(250) NOT NULL ,
  `type_message` int(2) NOT NULL ,
  PRIMARY KEY (`id`),
  INDEX (`id`)
)  ENGINE=INNODB CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('channel_sub_email')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_sub_email')}(
  `process_id` int(10) UNSIGNED NOT NULL ,
  `process_title` varchar(150) NOT NULL ,
  PRIMARY KEY (`process_id`),
  INDEX (`process_id`)
)  ENGINE=INNODB CHARSET=utf8;

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (1, 'Products Import');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (2, 'Products Update QTY');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (3, 'Products Update');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (4, 'Orders Import');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (5, 'Products Export');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (6, 'Orders Export');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (7, 'Products Import Run Again');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (8, 'Products Update Run Again');

");

$installer->endSetup();

?>