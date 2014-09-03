<?php
/**
 * @category   Oro
 * @package    Oro_Friends
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class Oro_Friends_Model_Api
 */
class Oro_Friends_Model_Api extends Zend_Rest_Client
{
    /**
     * Constructor
     *
     * @param null|string|Zend_Uri_Http $uri URI for the web service
     */
    public function __construct($uri = null)
    {
        parent::__construct($uri);
        $httpClient = $this->getHttpClient();
        $httpClient->setConfig(array('timeout' => 30));
    }

    /**
     * @param string $path
     * @param array $params
     * @return Oro_Friends_Model_Api_Result
     * @throws Oro_Friends_Model_Api_Exception
     */
    public function get($path, $params = array())
    {
        return $this->_rest($path, $params, 'get');
    }

    /**
     * @param string $path
     * @param array $params
     * @return Oro_Friends_Model_Api_Result
     * @throws Oro_Friends_Model_Api_Exception
     */
    public function post($path, $params = array())
    {
        return $this->_rest($path, $params, 'post');
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $call
     * @return null|Oro_Friends_Model_Api_Result
     */
    protected function _rest($path, $params, $call = 'get')
    {
        /** @var Oro_Friends_Helper_Api $helper */
        $helper = Mage::helper('oro_friends/api');

        $sig = $helper->getSecret();

        ksort($params);

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = $helper->jsonEncode($value);
            }
            $params[$key] = $value;
            $sig .= $key.$value;
        }

        $params['sig'] = md5($sig);

        $result   = null;
        $response = null;

        try {

            $restMethod = 'rest' . ucfirst($call);
            if (!method_exists($this, $restMethod)) {
                throw new Oro_Friends_Model_Api_Exception ($helper->__('Incorrect REST Method'));
            }

            /** @var Zend_Http_Response $response */
            $response = $this->$restMethod($path, $params);

            if (200 != $response->getStatus()) {
                throw new Oro_Friends_Model_Api_Exception (
                    $helper->__('Server returned response with status code: %d', $response->getStatus())
                );
            }

            $result = new Oro_Friends_Model_Api_Result($response->getBody());

            if (!$result->getData('success')) {
                throw new Oro_Friends_Model_Api_Exception (
                    $helper->__('Server returned error.')
                );
            }

        } catch (Oro_Friends_Model_Api_Exception $e) {
            Mage::log(sprintf("%s: path:%s, params:%s, errorMessage:%s", ($response instanceof Zend_Http_Response  ? $response->getStatus() : null), $path, implode('|',$params), $e->getMessage()),
                null,
                '500friends.api.log',
                true
            );
        } catch (Exception $e) {
            Mage::log(sprintf("message: %s, path: %s, params: %s", $e->getMessage(), $path, implode('|',$params)),
                null,
                '500friends.api.log',
                true
            );
        }

        return $result;
    }

    /**
     * Disable call method functional, we supports only GET request
     *
     * @param string $method
     * @param array $args
     * @return void|Zend_Rest_Client|Zend_Rest_Client_Result
     * @throws Exception
     */
    public function __call($method, $args)
    {
        throw new Exception('Method ' . $method . ' not exists');
    }
}
