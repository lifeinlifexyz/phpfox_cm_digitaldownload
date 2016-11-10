<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

Interface IRule
{
    /**
     * @param $aMessages
     * @return $this
     */
    public function setErrorMessage($aMessages);
}