<?php

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Api_Abstract extends Ess_M2ePro_Model_Connector_Command
{
    const COMPONENT = 'Api';
    const COMPONENT_VERSION = 2;

    // ########################################

    protected function getComponent()
    {
        return self::COMPONENT;
    }

    protected function getComponentVersion()
    {
        return self::COMPONENT_VERSION;
    }

    // ########################################
}