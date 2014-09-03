<?php
/*
  Class to override the uploadAndImport method for matrixrate shipping methods
*/
class Oro_Betterest_Model_Matrixrate_Mysql4_Carrier_Matrixrate extends Webshopapps_Matrixrate_Model_Mysql4_Carrier_Matrixrate 
{


    public function uploadAndImport(Varien_Object $object)
    {
        $csvFile = $_FILES["groups"]["tmp_name"]["matrixrate"]["fields"]["import"]["value"];

        if (!empty($csvFile)) {

            $csv = trim(file_get_contents($csvFile));

            $table = Mage::getSingleton('core/resource')->getTableName('matrixrate_shipping/matrixrate');
            $websiteId = $object->getScopeId();
            $websiteModel = Mage::app()->getWebsite($websiteId);
            /*
            getting condition name from post instead of the following commented logic
            */

            if (isset($_POST['groups']['matrixrate']['fields']['condition_name']['inherit'])) {
                $conditionName = (string)Mage::getConfig()->getNode('default/carriers/matrixrate/condition_name');
            } else {
                $conditionName = $_POST['groups']['matrixrate']['fields']['condition_name']['value'];
            }

//            $conditionName = $object->getValue();
//            if ($conditionName{0} == '_') {
//                $conditionName = Mage::helper('core/string')->substr($conditionName, 1, strpos($conditionName, '/')-1);
//            } else {
//                $conditionName = $websiteModel->getConfig('carriers/matrixrate/condition_name');
//            }
            $conditionFullName = Mage::getModel('matrixrate_shipping/carrier_matrixrate')->getCode('condition_name_short', $conditionName);
            if (!empty($csv)) {
                $exceptions = array();
                $csvLines = explode("\n", $csv);
                $csvLine = array_shift($csvLines);
                $csvLine = $this->_getCsvValues($csvLine);
                if (count($csvLine) < 7) {
                    $exceptions[0] = Mage::helper('shipping')->__('Invalid Matrix Rates File Format');
                }

                $countryCodes = array();
                $regionCodes = array();
                foreach ($csvLines as $k=>$csvLine) {
                    $csvLine = $this->_getCsvValues($csvLine);
                    if (count($csvLine) > 0 && count($csvLine) < 7) {
                        $exceptions[0] = Mage::helper('shipping')->__('Invalid Matrix Rates File Format');
                    } else {
                        $countryCodes[] = $csvLine[0];
                        $regionCodes[] = $csvLine[1];
                    }
                }

                if (empty($exceptions)) {
                    $data = array();
                    $countryCodesToIds = array();
                    $regionCodesToIds = array();
                    $countryCodesIso2 = array();

                    $countryCollection = Mage::getResourceModel('directory/country_collection')->addCountryCodeFilter($countryCodes)->load();
                    foreach ($countryCollection->getItems() as $country) {
                        $countryCodesToIds[$country->getData('iso3_code')] = $country->getData('country_id');
                        $countryCodesToIds[$country->getData('iso2_code')] = $country->getData('country_id');
                        $countryCodesIso2[] = $country->getData('iso2_code');
                    }

     				$regionCollection = Mage::getResourceModel('directory/region_collection')
                        ->addRegionCodeFilter($regionCodes)
                        ->addCountryFilter($countryCodesIso2)
                        ->load();

                    foreach ($regionCollection->getItems() as $region) {
                        $regionCodesToIds[$countryCodesToIds[$region->getData('country_id')]][$region->getData('code')] = $region->getData('region_id');
                    }

                    $shippingNameMaps = array();
                    foreach ($csvLines as $k=>$csvLine) {

                        $csvLine = $this->_getCsvValues($csvLine);

                        if(count($csvLine) == 10){
                            $shippingNameMaps[] = $csvLine[9];
                        }else{
                            $shippingNameMaps[] = '';
                        }

                        if (empty($countryCodesToIds) || !array_key_exists($csvLine[0], $countryCodesToIds)) {
                            $countryId = '0';
                            if ($csvLine[0] != '*' && $csvLine[0] != '') {
                                $exceptions[] = Mage::helper('shipping')->__('Invalid Country "%s" in the Row #%s', $csvLine[0], ($k+1));
                            }
                        } else {
                            $countryId = $countryCodesToIds[$csvLine[0]];
                        }

                        if (!isset($countryCodesToIds[$csvLine[0]])
                            || !isset($regionCodesToIds[$countryCodesToIds[$csvLine[0]]])
                            || !array_key_exists($csvLine[1], $regionCodesToIds[$countryCodesToIds[$csvLine[0]]])) {
                            $regionId = '0';
                            if ($csvLine[1] != '*' && $csvLine[1] != '') {
                                $exceptions[] = Mage::helper('shipping')->__('Invalid Region/State "%s" in the Row #%s', $csvLine[1], ($k+1));
                            }
                        } else {
                            $regionId = $regionCodesToIds[$countryCodesToIds[$csvLine[0]]][$csvLine[1]];
                        }

						if (count($csvLine)==10) {

							// we are searching for postcodes in ranges & including cities
							if ($csvLine[2] == '*' || $csvLine[2] == '') {
								$city = '';
							} else {
								$city = $csvLine[2];
							}


							if ($csvLine[3] == '*' || $csvLine[3] == '') {
								$zip = '';
							} else {
								$zip = $csvLine[3];
							}


							if ($csvLine[4] == '*' || $csvLine[4] == '') {
								$zip_to = '';
							} else {
								$zip_to = $csvLine[4];
							}


							if (!$this->_isPositiveDecimalNumber($csvLine[5]) || $csvLine[5] == '*' || $csvLine[5] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid %s From "%s" in the Row #%s', $conditionFullName, $csvLine[5], ($k+1));
							} else {
								$csvLine[5] = (float)$csvLine[5];
							}

							if (!$this->_isPositiveDecimalNumber($csvLine[6])) {
								$exceptions[] = Mage::helper('shipping')->__('Invalid %s To "%s" in the Row #%s', $conditionFullName, $csvLine[6], ($k+1));
							} else {
								$csvLine[6] = (float)$csvLine[6];
							}


							$data[] = array('website_id'=>$websiteId, 'dest_country_id'=>$countryId, 'dest_region_id'=>$regionId, 'dest_city'=>$city, 'dest_zip'=>$zip, 'dest_zip_to'=>$zip_to, 'condition_name'=>$conditionName, 'condition_from_value'=>$csvLine[5],'condition_to_value'=>$csvLine[6], 'price'=>$csvLine[7], 'delivery_type'=>$csvLine[8]);

						}
						else {

							if ($csvLine[2] == '*' || $csvLine[2] == '') {
								$zip = '';
							} else {
								$zip = $csvLine[2]."%";
							}

							$city='';
							$zip_to = '';

							if (!$this->_isPositiveDecimalNumber($csvLine[3]) || $csvLine[3] == '*' || $csvLine[3] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid %s From "%s" in the Row #%s', $conditionFullName, $csvLine[3], ($k+1));
							} else {
								$csvLine[3] = (float)$csvLine[3];
							}

							if (!$this->_isPositiveDecimalNumber($csvLine[4])) {
								$exceptions[] = Mage::helper('shipping')->__('Invalid %s To "%s" in the Row #%s', $conditionFullName, $csvLine[4], ($k+1));
							} else {
								$csvLine[4] = (float)$csvLine[4];
							}
							$data[] = array('website_id'=>$websiteId, 'dest_country_id'=>$countryId, 'dest_region_id'=>$regionId,  'dest_city'=>$city,'dest_zip'=>$zip,'dest_zip_to'=>$zip_to,  'condition_name'=>$conditionName, 'condition_from_value'=>$csvLine[3],'condition_to_value'=>$csvLine[4], 'price'=>$csvLine[5], 'delivery_type'=>$csvLine[6]);
						}


						$dataDetails[] = array('country'=>$csvLine[0], 'region'=>$csvLine[1]);

                    }
                }
                if (empty($exceptions)) {
                    $connection = $this->_getWriteAdapter();


                    $condition = array(
                        $connection->quoteInto('website_id = ?', $websiteId),
                        $connection->quoteInto('condition_name = ?', $conditionName),
                    );
                    $connection->delete($table, $condition);
                    
                    $this->_emptyShippingMap($connection);
                    
                    foreach($data as $k=>$dataLine) {
                        try {
                           $connection->insert($table, $dataLine);

                           if(!empty($shippingNameMaps[$k])){
                            $this->_reMapShipping($shippingNameMaps[$k], $connection->lastInsertId());
                           }

                        } catch (Exception $e) {
                            $exceptions[] = Mage::helper('shipping')->__('Duplicate Row #%s (Country "%s", Region/State "%s", City "%s", Zip From "%s", Zip To "%s", Delivery Type "%s", Value From "%s" and Value To "%s")', ($k+1), $dataDetails[$k]['country'], $dataDetails[$k]['region'], $dataLine['dest_city'], $dataLine['dest_zip'],  $dataLine['dest_zip_to'], $dataLine['delivery_type'], $dataLine['condition_from_value'], $dataLine['condition_to_value']);
                        }
                    }
                }
                if (!empty($exceptions)) {
                    throw new Exception( "\n" . implode("\n", $exceptions) );
                }
            }
        }
    }

    private function _getCsvValues($string, $separator=",")
    {
        $elements = explode($separator, trim($string));
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes %2 == 1) {
                for ($j = $i+1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') > 0) {
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j-$i+1, implode($separator, array_slice($elements, $i, $j-$i+1)));
                        break;
                    }
                }
            }
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

    private function _isPositiveDecimalNumber($n)
    {
        return preg_match ("/^[0-9]+(\.[0-9]*)?$/", $n);
    }

    protected function _emptyShippingMap($connection){
        try{
            $table = Mage::getSingleton('core/resource')->getTableName('betterest/shippingmap');
            $connection->delete($table);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    protected function _reMapShipping($nameMap, $matrixrateId)
    {
        try{
            Mage::getModel("betterest/shippingmap")
                ->setInternalShippingMethod('matrixrate_matrixrate_'.$matrixrateId)
                ->setExternalShippingMethod($nameMap)
                ->save();
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }

    }


}