<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Block_Adminhtml_Sales_Order_Grid_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if (!$currentData = $row->getData($this->getColumn()->getIndex())) {
            return 'No Uploaded File';
        }
        
        $downloadUrl = $this->getUrl('orderattach/adminhtml_order/download', array('file' => $this->_getValue($row)));
        return '<a href="'. $downloadUrl .'">' . $this->_getValue($row) . '</a>';
    }
}