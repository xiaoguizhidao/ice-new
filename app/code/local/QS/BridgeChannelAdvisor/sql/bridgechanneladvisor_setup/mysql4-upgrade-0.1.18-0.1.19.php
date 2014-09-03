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

DROP TABLE IF EXISTS {$this->getTable('channel_prepare_items')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_prepare_items')}(
  `item_id` int(10) unsigned NOT NULL auto_increment ,
  `product_id` int(10) NOT NULL,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY (`product_id`),
  INDEX (`item_id`)
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>