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

		ALTER TABLE `{$this->getTable('sales_flat_order')}`
				add column channeladvisor_order_id int(10)
	");

$installer->endSetup();