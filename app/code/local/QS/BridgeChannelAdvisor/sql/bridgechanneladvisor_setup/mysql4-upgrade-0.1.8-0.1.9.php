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
		ALTER TABLE `{$this->getTable('catalog_product_entity')}`
				add column channeladvisor_product smallint (1) DEFAULT '0'
	");

$installer->endSetup();