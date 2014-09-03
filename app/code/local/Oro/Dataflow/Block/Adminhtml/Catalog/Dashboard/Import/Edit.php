<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Import_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId   = 'id';
    protected $_controller = 'adminhtml_catalog_dashboard_import';
    protected $_blockGroup = 'oro_dataflow';

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('New Catalog Import Task');
    }

}
