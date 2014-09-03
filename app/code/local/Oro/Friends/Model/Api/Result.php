<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Model_Api_Result
 */
class Oro_Friends_Model_Api_Result
    extends Varien_Object
{
    /**
     * @param string $jsonString
     * @return $this
     */
    public function setJson($jsonString)
    {
        /** @var Oro_Friends_Helper_Data $helper */
        $helper = Mage::helper('oro_friends');
        $this->setData($helper->jsonDecode($jsonString));

        return $this;
    }

    /**
     * @param string $responseText
     * @throws Oro_Friends_Model_Api_Exception
     */
    public function __construct($responseText = '')
    {
        try {
            if (strlen($responseText)) {
                /** @var Mage_Core_Helper_Data $helper */
                $helper = Mage::helper('core');
                $result = $helper->jsonDecode($responseText);
                parent::__construct($result);
            } else {
                parent::__construct();
            }
        } catch (Exception $e) {
            throw new Oro_Friends_Model_Api_Exception($e->getMessage());
        }
    }
}
