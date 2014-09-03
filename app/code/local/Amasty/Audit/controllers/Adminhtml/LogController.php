<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
class Amasty_Audit_Adminhtml_LogController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() 
    {
        $this->loadLayout(); 
        $this->_setActiveMenu('system/amaudit');
        $this->_title($this->__('Action Log'));
               
        $this->_addBreadcrumb($this->__('Audit Log'), $this->__('Action Log')); 
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_userlog');
        $this->_addContent($block);         
        $this->renderLayout();
    }
    
    public function ajaxAction() 
    {
        $idItem = Mage::app()->getRequest()->getParam('idItem');
        Mage::register('current_log', Mage::getModel('amaudit/log')->load($idItem));
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_userlog_edit_tab_view_details');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function editAction() 
    {
        $this->loadLayout(); 
        $entityId  = (int) $this->getRequest()->getParam('id');
        $logEntity = Mage::getModel('amaudit/log')->load($entityId);

        if ($entityId && !$logEntity->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This item no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        
        if (!is_null(Mage::registry('current_log')))
        {
            Mage::unregister('current_log');
        }
        Mage::register('current_log', $logEntity);

        $this->_title($logEntity->getCategoryName());

        $this->_setActiveMenu('system/amaudit');
        $this->renderLayout();
    }
    
    public function exportCsvAction()
    {
            $fileName   = 'Auditlog.csv';
            $content    = $this->getLayout()->createBlock('amaudit/adminhtml_userlog_grid_export')
                ->getCsv();

            $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
            $fileName   = 'Auditlog.xml';
            $content    = $this->getLayout()->createBlock('amaudit/adminhtml_userlog_grid_export')
                ->getXml();

            $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK','');
            $response->setHeader('Pragma', 'public', true);
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
            $response->setHeader('Last-Modified', date('r'));
            $response->setHeader('Accept-Ranges', 'bytes');
            $response->setHeader('Content-Length', strlen($content));
            $response->setHeader('Content-type', $contentType);
            $response->setBody($content);
            $response->sendResponse();
            die;
    }
}