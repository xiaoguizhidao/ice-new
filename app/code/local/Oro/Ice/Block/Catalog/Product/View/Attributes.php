<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Ice_Block_Catalog_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes
{

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()
                && !in_array($attribute->getAttributeCode(), $excludeAttr)
                && trim($product->getData($attribute->getAttributeCode()))
            ) {
                $value = $attribute->getFrontend()->getValue($product);

                if ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    if ($attribute->getFrontendClass() == 'col-label') {
                        if (!isset($data[$attribute->getAttributeGroupId()]['col-header'])) {
                            $data[$attribute->getAttributeGroupId()]['col-header'] = array(
                                'label' => '',
                                'value' => '',
                                'code' => '',
                            );
                        }
                        $data[$attribute->getAttributeGroupId()]['col-header']['label'] = $value;
                    } elseif ($attribute->getFrontendClass() == 'col-value') {
                        if (!isset($data[$attribute->getAttributeGroupId()]['col-header'])) {
                            $data[$attribute->getAttributeGroupId()]['col-header'] = array(
                                'label' => '',
                                'value' => '',
                                'code' => '',
                            );
                        }
                        $data[$attribute->getAttributeGroupId()]['col-header']['value'] = $value;
                    } else {
                        $data[$attribute->getAttributeGroupId()][$attribute->getAttributeCode()] = array(
                            'label' => $attribute->getStoreLabel(),
                            'value' => $value,
                            'code'  => $attribute->getAttributeCode(),
                            'class' => $attribute->getFrontendClass()
                        );
                    }
                }
            }
        }

        return $data;
    }

} 
