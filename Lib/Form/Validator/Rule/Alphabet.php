<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Alphabet extends AbstractRule
{
    public function validate($sField, $sValue)
    {
        if (!preg_match("/[a-z,A-Z,_]{1,}/", $sValue)) {
            $sMessage = empty($this->sErrorMessage)
                ? 'The "'. $sField .'" value must contains "a"-"Z" and "_" chars'
                : $this->sErrorMessage;
            $this->oValidator->addError($sField, $sMessage);
        }
    }
}