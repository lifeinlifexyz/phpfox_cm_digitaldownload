<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Url extends AbstractRule
{
    protected $sErrorMessage = 'Invalid Url';

    public function validate($sField, $sValue)
    {
        if (!filter_var($sValue, FILTER_VALIDATE_URL)) {
            $this->oValidator->addError($sField, $this->sErrorMessage);
        }
    }
}