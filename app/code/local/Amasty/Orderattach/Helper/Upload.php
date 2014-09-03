<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Helper_Upload extends Mage_Core_Helper_Abstract
{
    public function getUploadUrl()
    {
        $url = Mage::getBaseUrl('media') . 'attachments/';
        return $url;
    }
    
    public function getUploadDir()
    {
        $dir = Mage::getBaseDir('media') . '/attachments/';
        return $dir;
    }
    
    public function cleanFileName($fileName)
    {
        return preg_replace('/[^a-zA-Z0-9_\.]/', '', $fileName);
    }
}