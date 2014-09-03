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

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (9, 'Products QTY Export');

INSERT INTO {$this->getTable('channel_sub_email')} (`process_id` ,`process_title`)
VALUES (10, 'Products Update QTY Run Again');

");

$installer->endSetup();

?>