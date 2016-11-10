<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class Max extends AbstractRule
{

    public function validate($sField, $iValue, $iMax)
    {
        if (((int)$iValue) > $iMax) {
            $sMessage = empty($this->sErrorMessage)
                ? 'Can not be larger than ' . $iMax
                : $this->sErrorMessage;

            $this->oValidator->addError($sField, $sMessage);
        }
    }
}