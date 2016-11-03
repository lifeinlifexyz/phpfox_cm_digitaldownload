<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Email extends AbstractRule
{
    protected $sErrorMessage = 'Invalid email';

    public function validate($sField, $sValue)
    {
        if (!filter_var($sValue, FILTER_VALIDATE_EMAIL)) {
            $this->oValidator->addError($sField, $this->sErrorMessage);
        }
    }
}