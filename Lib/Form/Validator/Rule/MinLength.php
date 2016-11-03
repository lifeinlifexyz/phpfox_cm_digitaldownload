<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class MinLength extends AbstractRule
{

    public function validate($sField, $sValue, $iMax)
    {
        if (strlen($sValue) < $iMax) {
            $sMessage = empty($this->sErrorMessage)
                ? 'Can not be less than ' . $iMax . ' characters'
                : $this->sErrorMessage;

            $this->oValidator->addError($sField, $sMessage);
        }
    }
}