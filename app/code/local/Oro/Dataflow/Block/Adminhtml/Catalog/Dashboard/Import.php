<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Import
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'oro_dataflow';
        $this->_controller = 'adminhtml_catalog_dashboard_import';
        $this->_headerText = Mage::helper('oro_dataflow')->__('Catalog Import');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('oro_dataflow')->__('Schedule New Import'));
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/newImport');
    }
}
