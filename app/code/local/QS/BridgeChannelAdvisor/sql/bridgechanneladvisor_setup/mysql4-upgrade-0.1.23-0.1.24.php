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

TRUNCATE TABLE {$this->getTable('channel_dynamic_configs')};

INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('max_allowed_packet', '32');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('max_execution_time', '18000');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('memory_limit', '1024');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('soap', '1');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('ioncube', '1');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('email_send', '0');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('status_check', '1');
INSERT INTO {$this->getTable('channel_dynamic_configs')} (config_key, config_value) VALUES ('cron_check', '1');

");

$installer->endSetup();

?>