<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Validator\Rule;

use Apps\CM_DigitalDownload\Lib\Form\Validator\IValidator;

abstract class AbstractRule implements IRule
{
    protected $oValidator;
    protected $sErrorMessage;

    public function __construct(IValidator &$oValidator)
    {
        $this->oValidator = $oValidator;
    }

    /**
     * @param $sMessage
     * @return $this
     */
    public function setErrorMessage($sMessage)
    {
        $this->sErrorMessage = $sMessage;
        return $this;
    }
}