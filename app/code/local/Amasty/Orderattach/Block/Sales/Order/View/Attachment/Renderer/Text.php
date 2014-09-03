<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Block_Sales_Order_View_Attachment_Renderer_Text extends Amasty_Orderattach_Block_Sales_Order_View_Attachment_Renderer_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amorderattach/renderer/text.phtml');
    }
}