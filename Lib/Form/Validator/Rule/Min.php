<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Min extends AbstractRule
{
    public function validate($sField, $iValue, $iMin)
    {
        if (((int)$iValue) < $iMin) {
            $sMessage = empty($this->sErrorMessage)
                ? 'Can not be less than ' . $iMin
                : $this->sErrorMessage;

            $this->oValidator->addError($sField, $sMessage);
        }
    }
}