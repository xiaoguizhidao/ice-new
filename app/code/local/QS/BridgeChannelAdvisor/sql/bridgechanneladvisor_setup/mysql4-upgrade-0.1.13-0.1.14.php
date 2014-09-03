<?php
/**
 * ChannelAdvisor System Settings
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

$installer = $this;
$installer->startSetup();
$installer->run("

INSERT INTO {$this->getTable('channel_settings')} (`id` ,`page_cnt`, `products_count`, `channel_flag`, `title`, `status`)
VALUES (3, 1, 100, 0, 'Update Products Data from ChannelAdvisor', 0);

");

$installer->endSetup();

?>