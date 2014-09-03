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

DROP TABLE IF EXISTS {$this->getTable('channel_variation_relationships')};

CREATE TABLE IF NOT EXISTS {$this->getTable('channel_variation_relationships')}(
  `rel_id` int(10) unsigned NOT NULL auto_increment ,
  `relationship_name` varchar(255),
  `attributes` varchar(255),
  PRIMARY KEY (`rel_id`),
  INDEX (`rel_id`)
)  ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();

?>