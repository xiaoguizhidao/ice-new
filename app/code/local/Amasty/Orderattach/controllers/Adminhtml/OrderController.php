<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Orderattach
*/
class Amasty_Orderattach_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/amorderattach')
            ->_addBreadcrumb(Mage::helper('amorderattach')->__('System'), Mage::helper('sales')->__('System'))
            ->_addBreadcrumb(Mage::helper('amorderattach')->__('Manage Order Attachments'), Mage::helper('amorderattach')->__('Manage Order Attachments'))
        ;
        return $this;
    }
    
    protected function _initOrder()
    {
        $orderId = $this->getRequest()->getPost('order_id');
        $order   = Mage::getModel('sales/order')->load($orderId);
        Mage::register('current_order', $order);
    }
    
    protected function _sendResponse($fieldModel)
    {
        $this->getResponse()->setBody($fieldModel->getRenderer()->render());
    }

    public function saveAction()
    {
        $this->_initOrder();
        $fieldModel = Mage::getModel('amorderattach/field')->load($this->getRequest()->getPost('field'), 'fieldname');
        if ($fieldModel->getId())
        {
            $orderField = Mage::getModel('amorderattach/order_field')->load(Mage::registry('current_order')->getId(), 'order_id');
            if (!$orderField->getOrderId())
            {
                $orderField->setOrderId(Mage::registry('current_order')->getId());
            }
            if ('date' == $this->getRequest()->getPost('type'))
            {
                if ($this->getRequest()->getPost('value'))
                {
                    $value = date('Y-m-d', strtotime($this->getRequest()->getPost('value')));
                } else 
                {
                    $value = null;
                }
            } else 
            {
                $value = $this->getRequest()->getPost('value');
            }
            $orderField->setData($this->getRequest()->getPost('field'), $value);
            $orderField->save();
            
            $orderFieldLoad = Mage::getModel('amorderattach/order_field')->load(Mage::registry('current_order')->getId(), 'order_id');
            // updating "updated_at" ...
            if (Mage::getStoreConfig('amorderattach/general/update_updated_at'))
            {
                Mage::registry('current_order')->setUpdatedAt(Varien_Date::formatDate(Mage::getModel('core/date')->gmtTimestamp()))->save();
            }
            
            Mage::register('current_attachment_order_field', $orderFieldLoad); // required for renderer
            $this->_sendResponse($fieldModel);
        }
    }
    
    public function uploadAction()
    {
        $this->_initOrder();
        $fieldModel = Mage::getModel('amorderattach/field')->load($this->getRequest()->getPost('field'), 'fieldname');
        if ($fieldModel->getId())
        {
            $orderField = Mage::getModel('amorderattach/order_field')->load(Mage::registry('current_order')->getId(), 'order_id');
            if (!$orderField->getOrderId())
            {
                $orderField->setOrderId(Mage::registry('current_order')->getId());
            }
            
            // uploading file
            if (isset($_FILES['to_upload']['error']) && UPLOAD_ERR_OK == $_FILES['to_upload']['error'])
            {
                try 
                {
                    $fileName = $_FILES['to_upload']['name'];
                    $fileName = Mage::helper('amorderattach/upload')->cleanFileName($fileName);
                    $uploader = new Varien_File_Uploader('to_upload');
                    $uploader->setFilesDispersion(false);
                    $fileDestination = Mage::helper('amorderattach/upload')->getUploadDir();
                    if (file_exists($fileDestination . $fileName))
                    {
                        $fileName = uniqid(date('ihs')) . $fileName;
                    }
                    $uploader->save($fileDestination, $fileName);
                } catch (Exception $e) 
                {
                    $this->_getSession()->addException($e, Mage::helper('amorderattach')->__('An error occurred while saving the file: ') . $e->getMessage());
                }
                if ('file' == $this->getRequest()->getPost('type')) // each new overwrites old one
                {
                    $orderField->setData($this->getRequest()->getPost('field'), $fileName);
                }
                if ('file_multiple' == $this->getRequest()->getPost('type'))
                {
                    $fieldData = $orderField->getData($this->getRequest()->getPost('field'));
                    $fieldData = explode(';', $orderField->getData($this->getRequest()->getPost('field')));
                    $fieldData[] = $fileName;
                    $fieldData = implode(';', $fieldData);
                    $orderField->setData($this->getRequest()->getPost('field'), $fieldData);
                }
                $orderField->save();
                echo 'success'; exit;
            }
        }
        echo 'failed'; exit;
    }
    
    public function downloadAction()
    {
        $fileName = $this->getRequest()->getParam('file');
        $fileName = Mage::helper('amorderattach/upload')->cleanFileName($fileName);
        if (file_exists(Mage::helper('amorderattach/upload')->getUploadDir() . $fileName))
        {
            header('Content-Disposition: attachment; filename="' . $fileName . '"');               
            if(function_exists('mime_content_type')) 
            {
                header('Content-Type: ' . mime_content_type(Mage::helper('amorderattach/upload')->getUploadDir() . $fileName));                    
            }
            else if(class_exists('finfo'))
            {
                 $finfo = new finfo(FILEINFO_MIME);
                 $mimetype = $finfo->file(Mage::helper('amorderattach/upload')->getUploadDir() . $fileName);
                 header('Content-Type: ' . $mimetype);
            }                
            readfile(Mage::helper('amorderattach/upload')->getUploadDir() . $fileName); 
        }
        exit;
    }
    
    public function deleteAction()
    {
        $this->_initOrder();
        $fieldModel = Mage::getModel('amorderattach/field')->load($this->getRequest()->getPost('field'), 'fieldname');
        if ($fieldModel->getId())
        {
            $orderField = Mage::getModel('amorderattach/order_field')->load(Mage::registry('current_order')->getId(), 'order_id');
            if ($orderField->getOrderId())
            {
                $fileName = $this->getRequest()->getParam('file');
                if (file_exists(Mage::helper('amorderattach/upload')->getUploadDir() . $fileName))
                {
                    @unlink(Mage::helper('amorderattach/upload')->getUploadDir() . $fileName);
                }
                if ('file' == $this->getRequest()->getPost('type'))
                {
                    $value = '';
                } elseif ('file_multiple' == $this->getRequest()->getPost('type')) 
                {
                    $value = explode(';', $orderField->getData($this->getRequest()->getPost('field')));
                    foreach ($value as $key => $val)
                    {
                        if ($val == $fileName)
                        {
                            unset($value[$key]);
                        }
                    }
                    $value = implode(';', $value);
                }
                $orderField->setData($this->getRequest()->getPost('field'), $value);
                $orderField->save();
                Mage::register('current_attachment_order_field', $orderField); // required for renderer
            }
            $this->_sendResponse($fieldModel);
        }
    }
    
    public function reloadAction()
    {
        $this->_initOrder();
        $fieldModel = Mage::getModel('amorderattach/field')->load($this->getRequest()->getPost('field'), 'fieldname');
        if ($fieldModel->getId())
        {
            $orderField = Mage::getModel('amorderattach/order_field')->load(Mage::registry('current_order')->getId(), 'order_id');
            Mage::register('current_attachment_order_field', $orderField); // required for renderer
            $this->_sendResponse($fieldModel);
        }
    }
}
