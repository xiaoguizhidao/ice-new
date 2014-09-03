<?php
/**
 * ChannelAdvisor Content ShipType resource model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Shiptype extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('bridgechanneladvisor/shiptype', 'ship_type_id');
    }

    /**
     * Return Type ID by Attribute Set Id
     *
     * @param QS_BridgeChannelAdvisor_Model_Type $model
     * @param int $attributeSetId Attribute Set
     * @return QS_BridgeChannelAdvisor_Model_Type
     */
    /* public function loadByAttributeSetId($model, $attributeSetId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('attribute_set_id=?', $attributeSetId);

        $data = $this->_getReadAdapter()->fetchRow($select);
        $data = is_array($data) ? $data : array();
        $model->setData($data);
        return $model;
    } */
}
