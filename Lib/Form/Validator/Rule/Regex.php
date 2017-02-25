<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Regex extends AbstractRule
{
    protected $sErrorMessage = 'Do not match with regex expression';

    public function validate($sField, $sValue, $sRegex)
    {
        if (!preg_match($sRegex, $sValue)) {
            $this->oValidator->addError($sField, $this->sErrorMessage);
        }
    }
}