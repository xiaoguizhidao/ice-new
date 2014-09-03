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
ALTER TABLE {$this->getTable('channel_types')}
    ADD CONSTRAINT `FK_channel_category_rule` FOREIGN KEY (`category_id`)
    REFERENCES {$this->getTable('channel_categories')} (`category_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('channel_pair_attributes')}
    ADD CONSTRAINT `FK_ca_attribute_rule` FOREIGN KEY (`ca_attribute_id`)
    REFERENCES {$this->getTable('channel_attributes')} (`attribute_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('channel_relations')}
    ADD CONSTRAINT `FK_ca_attr_rule` FOREIGN KEY (`attribute_id`)
    REFERENCES {$this->getTable('channel_attributes')} (`attribute_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
");

$installer->endSetup();
