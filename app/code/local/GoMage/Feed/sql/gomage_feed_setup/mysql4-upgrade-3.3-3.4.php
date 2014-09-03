<?php
 /**
 * GoMage.com
 *
 * GoMage Feed Pro
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2013 GoMage.com (http://www.gomage.com)
 * @author       GoMage.com
 * @license      http://www.gomage.com/licensing  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 3.3
 * @since        Class available since Release 3.3
 */

$installer = $this;
$installer->startSetup();

Mage::getConfig()->saveConfig(
    'gomage_feedpro/amazon/config',
    'a:3:{s:32:"generator_settings_'.uniqid().'";a:4:{s:4:"type";s:1:"0";s:5:"value";s:0:"";s:6:"action";s:1:"0";s:10:"input_code";s:3:"upc";}s:32:"generator_settings_'.uniqid().'";a:4:{s:4:"type";s:1:"0";s:5:"value";s:0:"";s:6:"action";s:1:"0";s:10:"input_code";s:3:"ean";}s:32:"generator_settings_'.uniqid().'";a:4:{s:4:"type";s:1:"0";s:5:"value";s:0:"";s:6:"action";s:1:"0";s:10:"input_code";s:3:"mpn";}}'
);
Mage::getConfig()->cleanCache();

$installer->endSetup();
