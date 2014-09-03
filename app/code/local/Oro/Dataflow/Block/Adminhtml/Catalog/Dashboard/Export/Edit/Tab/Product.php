<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Export_Edit_Tab_Product
    extends Mage_Core_Block_Text_List
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Product Information');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
}
