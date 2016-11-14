<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Numeric extends AbstractRule
{
    public function validate($sField, $sValue)
    {
        if (!is_numeric($sValue)) {
            $sMessage = empty($this->sErrorMessage)
                ? 'The "'. $sField .'" is not numeric value'
                : $this->sErrorMessage;
            $this->oValidator->addError($sField, $sMessage);
        }
    }
}