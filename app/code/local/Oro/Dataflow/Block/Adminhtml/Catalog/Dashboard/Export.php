<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Export
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'oro_dataflow';
        $this->_controller = 'adminhtml_catalog_dashboard_export';
        $this->_headerText = Mage::helper('oro_dataflow')->__('Catalog Export');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('oro_dataflow')->__('Schedule New Export'));
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/newExport');
    }
}
