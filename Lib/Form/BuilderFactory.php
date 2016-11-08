<?php
namespace Apps\CM_DigitalDownload\Lib\Form;

use Apps\CM_DigitalDownload\Lib\Form\Validator\Validator;

class BuilderFactory
{


    public function make($oRequest = null, $oView = null, $oValidator = null)
    {
        $oRequest = is_null($oRequest) ? request() : $oRequest;
        $oView = is_null($oView) ?  \Core\Controller::$__view : $oView;
        $oValidator = is_null($oValidator) ? new Validator() : $oValidator;
        return new Builder($oRequest, $oView, $oValidator);
    }
}