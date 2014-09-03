<?php

/*
* @copyright  Copyright (c) 2013 by  ESS-UA.
*/

class Ess_M2ePro_Adminhtml_Wizard_AmazonNewAsinController
    extends Ess_M2ePro_Controller_Adminhtml_Common_WizardController
{
    //#############################################

    protected function getNick()
    {
        return 'amazonNewAsin';
    }

    //#############################################

    public function completeAction()
    {
        parent::completeAction();
        return $this->_redirectUrl($this->_getRefererUrl());
    }

    //#############################################
}