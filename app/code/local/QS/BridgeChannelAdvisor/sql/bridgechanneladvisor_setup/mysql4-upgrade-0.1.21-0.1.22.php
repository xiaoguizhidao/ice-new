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
ALTER TABLE {$this->getTable('channel_prepare_items')}
    add column `export_try` smallint(1)
");

$installer->endSetup();
