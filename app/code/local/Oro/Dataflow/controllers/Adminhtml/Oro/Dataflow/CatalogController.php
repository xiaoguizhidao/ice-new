<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
class Oro_Dataflow_Adminhtml_Oro_Dataflow_CatalogController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init action
     *
     * @return $this
     */
    protected function _initLayout()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog');

        return $this;
    }

    /**
     * Redirect to dashboard
     */
    public function indexAction()
    {
        $this->_redirect('*/*/dashboard');
    }

    /**
     * Render dashboard
     */
    public function dashboardAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Schedule ajax grid
     */
    public function exportGridAction()
    {
        $this->loadLayout(array('adminhtml_oro_dataflow_catalog_dashboard_export_grid'));
        $this->renderLayout();
    }

    /**
     * Schedule ajax grid
     */
    public function importGridAction()
    {
        $this->loadLayout('adminhtml_oro_dataflow_catalog_dashboard_import_grid');
        $this->renderLayout();
    }

    /**
     * Schedule form
     */
    public function newImportAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Schedule from
     */
    public function newExportAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Schedule info
     */
    public function detailsExportAction()
    {
        $schedule = Mage::getModel('oro_dataflow/schedule_catalog_export')->load($this->getRequest()->getParam('id'));
        Mage::register('schedule', $schedule);

        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Schedule info
     */
    public function detailsImportAction()
    {
        $schedule = Mage::getModel('oro_dataflow/schedule_catalog_import')->load($this->getRequest()->getParam('id'));
        Mage::register('schedule', $schedule);

        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Remove schedule
     */
    public function deleteExportAction()
    {
        $schedule = Mage::getModel('oro_dataflow/schedule_catalog_export')->load($this->getRequest()->getParam('id'));
        if (Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING == $schedule->getData('status')) {
            $schedule->delete();
            $this->_getSession()->addSuccess($this->__('Task deleted successfully.'));
        } else {
            $this->_getSession()->addError($this->__('You can delete only pending tasks.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Remove schedule
     */
    public function deleteImportAction()
    {
        $schedule = Mage::getModel('oro_dataflow/schedule_catalog_import')->load($this->getRequest()->getParam('id'));
        if (Oro_Dataflow_Model_Schedule_Interface::STATUS_PENDING == $schedule->getData('status')) {
            $schedule->delete();
            $this->_getSession()->addSuccess($this->__('Task deleted successfully.'));
        } else {
            $this->_getSession()->addError($this->__('You can delete only pending tasks.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Save schedule date
     */
    public function saveExportAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                $schedule = Mage::getModel('oro_dataflow/schedule_catalog_export');
                $schedule->setData(array(
                    'user_id' => Mage::getSingleton('admin/session')->getUser()->getId(),
                    'created_at' => Mage::getSingleton('core/date')->gmtDate(),
                    'updated_at' => Mage::getSingleton('core/date')->gmtDate(),
                    'scheduled_at_date' => $data['scheduled_at_date'],
                    'scheduled_at_time' => $data['scheduled_at_time'],
                    'ids' => $data['ids'] ? explode('&', $data['ids']) : null,
                ));
                $schedule->save();

                $this->_getSession()->addSuccess(Mage::helper('oro_dataflow')->__('Profile saved successfully'));

                $this->_redirect('*/*');
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/newExport');
        }
    }

    /**
     * Save schedule date
     */
    public function saveImportAction()
    {
        try {
            if ($this->getRequest()->isPost()) {

                if(isset($_FILES['file_path']['name']) && $_FILES['file_path']['name'] != '') {
                    $data = $this->getRequest()->getPost();
                    $schedule = Mage::getModel('oro_dataflow/schedule_catalog_import');
                    $schedule->setData(array(
                        'user_id' => Mage::getSingleton('admin/session')->getUser()->getId(),
                        'created_at' => Mage::getSingleton('core/date')->gmtDate(),
                        'updated_at' => Mage::getSingleton('core/date')->gmtDate(),
                        'scheduled_at_date' => $data['scheduled_at_date'],
                        'scheduled_at_time' => $data['scheduled_at_time'],
                    ));
                    $schedule->save();

                    $fileName  = $_FILES['file_path']['name']; //file name
                    $localPath = 'var' . DS . 'import' . DS . 'schedule' . DS . $schedule->getId();
                    $absPath   = Mage::getBaseDir() . DS . $localPath;

                    $uploader = new Varien_File_Uploader('file_path');
                    $uploader->setAllowedExtensions(array('csv', 'xml'));
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $result = $uploader->save($absPath, $fileName);

                    $schedule->setData('file_path', $localPath . DS . $result['file']);
                    $schedule->save();

                    $this->_getSession()->addSuccess(Mage::helper('oro_dataflow')->__('Profile saved successfully'));

                    $this->_redirect('*/*');
                } else {
                    Mage::throwException(Mage::helper('oro_dataflow')->__('File not uploaded.'));
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    /**
     * Custom products grid
     */
    public function newExportProductGridAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Download result file
     */
    public function downloadExportAction()
    {
        try {
            $schedule = Mage::getModel('oro_dataflow/schedule_catalog_export')->load($this->getRequest()->getParam('id'));

            if (!$schedule->getId()
                || Oro_Dataflow_Model_Schedule_Interface::STATUS_SUCCESS != $schedule->getData('status')
                || !is_file($schedule->getData('file_path'))) {
                Mage::throwException(Mage::helper('oro_dataflow')->__('Data file not found.'));
            }

            $archivePath = Mage::getBaseDir('tmp') . DS . basename($schedule->getData('file_path')).'.zip';

            $zip = new Zend_Filter_Compress_Zip(array('archive' => $archivePath));
            $zip->compress($schedule->getData('file_path'));

            /** @var $helper Mage_Downloadable_Helper_Download */
            $helper = Mage::helper('downloadable/download');
            $helper->setResource($archivePath, Mage_Downloadable_Helper_Download::LINK_TYPE_FILE);

            $fileName = basename($archivePath);
            $contentType = $helper->getContentType();

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true);

            if ($fileSize = $helper->getFilesize()) {
                $this->getResponse()
                    ->setHeader('Content-Length', $fileSize);
            }

            if ($contentDisposition = $helper->getContentDisposition()) {
                $this->getResponse()
                    ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
            }

            $this->getResponse()->clearBody();
            $this->getResponse()->sendHeaders();
            $helper->output();
            unlink($archivePath);
        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }
}
