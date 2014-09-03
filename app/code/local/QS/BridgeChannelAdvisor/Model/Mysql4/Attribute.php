<?php
/**
 * ChannelAdvisor Content Attribute resource model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("bridgechanneladvisor/attribute", "attribute_id");
    }

    /**
     * Get Attributes from csv file
     */
    public function importAndUpload(){
        $csvFile = $_FILES["groups"]["tmp_name"]["cadata"]["fields"]["import"]["value"];

        if (!empty($csvFile)) {
            $csvAttrs = array();
            $csv = trim(file_get_contents($csvFile));
            if (!empty($csv)) {
                $csvLines = explode("\n", $csv);
                foreach ($csvLines as $k=>$csvLine) {
                    $csvLine = $this->_getCsvValues($csvLine);
                    $str = '';
                    $attrType = '';
                    $badAttrType = str_split($csvLine[0]);
                    foreach($badAttrType as $letter){
                        if(ord($letter) != 0){
                            $attrType = $attrType.$letter;
                        }
                    }
                    if($attrType == 'All'){
                        $attrName = str_split($csvLine[1]);
                        foreach($attrName as $letter){
                            if(ord($letter) != 0){
                                $str = $str.$letter;
                            }
                        }
                        array_push($csvAttrs,$str);
                    }
                }
                $attribute = Mage::getModel('bridgechanneladvisor/attribute');
                //CA core attributes save
                foreach($csvAttrs as $attr){
                    $existAttribute = $attribute->getCollection()->addFieldToFilter('attribute_name', $attr)->load();
                    if($existAttribute->count() == 0){
                        //we do not have channeladvisor category with current name in magento db
                        try {
                            $attribute->setAttributeName($attr);
                            $attribute->save();
                            $attribute->unsetData();
                        }catch (Exception $e) {
                            $response['message'] = $e->getMessage();
                            $response['error'] = true;
                        }
                    }
                }

            }
        }
    }

    /**
     * Get values from file
     *
     * @param $string
     * @param string $separator
     * @return array
     *
     */
    private function _getCsvValues($string, $separator=",")
    {
        $elements = explode($separator, trim($string));
        if(count($elements) == 1){
            $elements = explode("\t", trim($string));
        }
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr =& $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
            $elements[$i] = trim($elements[$i]);
        }
        return $elements;
    }

}
