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
ALTER TABLE {$this->getTable('channel_items')}
    add column `published` timestamp NOT NULL AFTER `store_id`
");

$installer->run("
ALTER TABLE {$this->getTable('channel_items')}
    add column `expires` datetime null
");


$installer->endSetup();
