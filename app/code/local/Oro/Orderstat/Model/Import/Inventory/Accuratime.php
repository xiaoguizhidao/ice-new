<?php
require_once(Mage::getBaseDir('lib') . '/PHPExcel/PHPExcel.php');
require_once(Mage::getBaseDir('lib'). '/PHPExcel/Classes/PHPExcel/Reader/Excel5.php');

class Oro_Orderstat_Model_Import_Inventory_Accuratime extends Oro_Orderstat_Model_Import_Inventory_Abstract
{
    const FIELD_VENDOR_SKU = 0;
    const FIELD_SKU = 0;
    const FIELD_QTY = 1;
    const IMPORT_FIELD_QTY = 2;
    const FILENAME = 'accuratime_stock_';
    protected $_timestamp;

    /*
     *
     */
    public function update()
    {
        // fetch file from Accuratime and save it to the import directory
        $file = $this->getHelper()->getInventoryConfig('file_location');
        $newFile = $this->_getFilePath().'inventory_accuratime_import'.$this->_getTimeStamp().'.xls';
        $copied = copy($file, $newFile);

        if($copied){
            // load file and update
            $objPHPExcel = new PHPExcel_Reader_Excel5();
            $objPHPExcel->setPassword($this->getHelper()->getInventoryConfig('password'));
            $importCsv = new Varien_File_Csv();
            $stockData = array( array('sku', 'qty') );

            $excelObj= $objPHPExcel->load($newFile);
            if($excelObj){
                $rows = $excelObj->getActiveSheet()->toArray();
                // remove the headers;
                array_shift($rows);

                foreach($rows as $row){
                    if(empty($row)){
                        continue;
                    }

                    $stockData[] = array($row[static::FIELD_SKU], $row[static::IMPORT_FIELD_QTY]);
                }

                $importCsv->saveData($this->_getFileName(), $stockData);

                // the import method expects a file resource
                $csvFileHandle = fopen($this->_getFileName(), 'r');
                $this->import($csvFileHandle);
            }

        }else{
            $this->getHelper()->load('Could not copy Accuratime Inventory file');
        }
    }

    /*
     * The method passes off the reformatted stock file to the ImportExport module
     *
     * @param str file path for stock import
     */
    protected function _batchImport($file)
    {
        try{
            $prodImport = Mage::getModel('importexport/import_entity_product');
            $adapter = new Mage_ImportExport_Model_Import_Adapter_Csv($file);
            $prodImport->setSource($adapter);
            $prodImport->setParameters(array('behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE));
            $prodImport->validateData();
            $errorMessages = $prodImport->getErrorMessages();
            if(count($errorMessages)){
                foreach($errorMessages as $key => $err){
                    $msg = $this->getHelper()->__('%s in rows: %s', $key,  implode(',', $err));
                    $this->getHelper()->log($msg);
                }
            }
        }catch(Exception $e){
            $this->getHelper()->log($e->getMessage());
        }

    }

    /*
     * Gets Vendor specific helper
     */
    public function getHelper()
    {
        return Mage::helper('orderstat/accuratime');
    }

    /*
     * Generates a unique file based on the timestamp
     */
    public function _getFileName()
    {
        return $this->_getFilePath().static::FILENAME.$this->_getTimeStamp().'.csv';
    }

    public function _getFilePath()
    {
        return Mage::getBaseDir('var').'/Orderstat/Import/';
    }

    /*
     * Gets a timestamp and keeps it for the life of the class
     */
    public function _getTimestamp()
    {
        if(!$this->_timestamp){
            $this->_timestamp = time();
        }
        return $this->_timestamp;
    }

    protected function _getProductIds($skus)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('i' => $this->getMainTable()), array('product_id', 'type_id', 'vendor_sku'))
            ->joinLeft(array('r' => $this->getTable('catalog/product_relation')),
                'i.product_id = r.child_id',
                array('parent_id' => 'r.parent_id')
            )
            ->where('i.vendor_sku IN(?)', $skus);

        return $this->_getReadAdapter()->fetchAll($select);
    }


}