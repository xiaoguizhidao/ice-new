<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/  
class Amasty_Audit_Block_Adminhtml_Tooltip extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amaudit/tooltip.phtml');
    }
    
    public function getAjaxUrl()
    {
        $url = $this->getUrl('amaudit/ajax/ajax');
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "")
        {
            $url = str_replace('http:', 'https:', $url);
        }
        return $url;    
    }
}

