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

INSERT INTO {$this->getTable('channel_settings')} (`id` ,`page_cnt`, `products_count`, `channel_flag`, `title`, `status`)
VALUES (2, 0, 0, 0, 'Mage-ChannelAdvisor Products Synchronize', 0);

");

$installer->endSetup();

?>