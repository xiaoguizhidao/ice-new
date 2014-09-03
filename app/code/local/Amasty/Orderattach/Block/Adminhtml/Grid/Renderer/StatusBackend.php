<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Block_Adminhtml_Grid_Renderer_StatusBackend extends Amasty_Orderattach_Block_Adminhtml_Grid_Renderer_StatusAbstract
{
   public function render(Varien_Object $row)
   {
       $this->setType('status_backend');
       return parent::render($row);
   }
}