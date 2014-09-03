<?php
/**
 * ChannelAdvisor Type Model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Type extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('bridgechanneladvisor/type');
    }

    /**
     * Load type model by Attribute Set Id
     *
     * @param int $attributeSetId Attribute Set
     * @return QS_CridgeChannelAdvisor_Model_Type
     */
    public function loadByAttributeSetId($attributeSetId)
    {
        return $this->getResource()->loadByAttributeSetId($this, $attributeSetId);
    }

}
