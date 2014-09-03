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

DROP TABLE IF EXISTS {$this->getTable('channel_dynamic_configs')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_dynamic_configs')}(
  `id` int(10) unsigned NOT NULL auto_increment ,
  `config_key` varchar(255),
  `config_value` varchar(255),
  `current_value` varchar(255),
  PRIMARY KEY (`id`),
  INDEX (`id`)
)  ENGINE=INNODB CHARSET=utf8;
");

$installer->endSetup();

?>