<?php
/**
 * ChannelAdvisor Content Relationships resource model
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Model_Mysql4_Relationships extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("bridgechanneladvisor/relationships", "rel_id");
    }

    /**
     * Get Relationships data from csv file
     */
    public function relationshipsUpload(){
        $csvFile = $_FILES["groups"]["tmp_name"]["cadata"]["fields"]["relationships"]["value"];

        if(!empty($csvFile)){
            $csvRel = array();
            $csv = trim(file_get_contents($csvFile));
            if(!empty($csv)){
                $csvLines = explode("\n", $csv);
                foreach($csvLines as $k=>$csvLine){
                    if($k>0){
                        $csvLine = $this->_getCsvValues($csvLine);
                        $relName = '';
                        $relAttributes = '';
                        $relationshipsName = str_split($csvLine[0]);
                        $relAttrs = str_split($csvLine[3]);
                        foreach($relationshipsName as $letter){
                            if(ord($letter) != 0){
                                $relName = $relName.$letter;
                            }
                        }
                        foreach($relAttrs as $char){
                            if(ord($char) != 0){
                                $relAttributes = $relAttributes.$char;
                            }
                        }
                        $csvRel[] = array($relName, $relAttributes);
                    }
                }
                $relation = Mage::getModel('bridgechanneladvisor/relationships');
                //CA core attributes save
                foreach($csvRel as $k=>$val){
                    $existRelation = $relation->getCollection()->addFieldToFilter('relationship_name', $val[0])->load();
                    if($existRelation->count() == 0){
                        //we do not have channeladvisor relationship with current name in magento db
                        try {
                            $relation->setRelationshipName($val[0]);
                            $relation->setAttributes($val[1]);
                            $relation->save();
                            $relation->unsetData();
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
    private function _getCsvValues($string, $separator="\t")
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
