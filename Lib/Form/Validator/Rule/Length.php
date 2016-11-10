<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Length extends AbstractRule
{

    public function validate($sField, $sValue, $iMin, $iMax)
    {
        if (strlen($sValue) < $iMin || strlen($sValue) > $iMax) {
            $sMessage = empty($this->sErrorMessage)
                ? 'Can not be larger than ' . $iMax . '  and less than ' . $iMin . ' characters'
                : $this->sErrorMessage;

            $this->oValidator->addError($sField, $sMessage);
        }
    }
}