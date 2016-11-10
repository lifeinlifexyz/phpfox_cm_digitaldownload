<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

class In extends AbstractRule
{

    public function validate($sField, $iValue)
    {
        $aArr = [];
        for($i = 2; $i < count(func_get_args()); $i++) {
            $aArr[] = func_get_arg($i);
        }

        if (!in_array($iValue, $aArr)) {
            $sMessage = empty($this->sErrorMessage)
                ? 'The "'. $sField .'" value is not in "' . implode(', ', $aArr) . '"'
                : $this->sErrorMessage;
            $this->oValidator->addError($sField, $sMessage);
        }
    }
}