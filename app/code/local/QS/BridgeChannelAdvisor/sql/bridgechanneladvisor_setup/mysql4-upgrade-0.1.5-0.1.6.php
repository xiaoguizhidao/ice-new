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

DROP TABLE IF EXISTS {$this->getTable('channel_settings')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_settings')}(
  `id` int(10) unsigned NOT NULL auto_increment ,
  `page_cnt` int(10) NOT NULL ,
  `products_count` int(10) NOT NULL,
  `channel_flag` smallint(1),
  PRIMARY KEY (`id`),
  INDEX (`id`)
)  ENGINE=INNODB CHARSET=utf8;

INSERT INTO {$this->getTable('channel_settings')} (`id` ,`page_cnt`, `products_count`, `channel_flag`)
VALUES (NULL, 1, 100, 0);

");

$installer->endSetup();

?>