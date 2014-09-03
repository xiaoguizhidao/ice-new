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
ALTER TABLE {$this->getTable('channel_settings')}
    add column `title` VARCHAR(225) null DEFAULT 'Import Products from ChannelAdvisor'
");

$installer->run("
ALTER TABLE {$this->getTable('channel_settings')}
    add column `last_run` datetime null
");

$installer->run("
ALTER TABLE {$this->getTable('channel_settings')}
    add column `status` smallint(1) DEFAULT '0'
");

$installer->run("
ALTER TABLE {$this->getTable('channel_settings')}
    add column `errors` INT(5) DEFAULT '0'
");

$installer->run("
ALTER TABLE {$this->getTable('channel_settings')}
    add column `products_imported` INT(10) DEFAULT '0'
");

$installer->run("
ALTER TABLE {$this->getTable('channel_settings')}
    add column `pending` smallint(1) DEFAULT '0'
");

$installer->run("
ALTER TABLE {$this->getTable('channel_settings')}
    add column `all_ca_products` INT(10) DEFAULT '0'
");

$installer->endSetup();
