<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Validator;

interface IValidator
{
    /**
     * @param $aRules array
     * @return $this
     */
    public function setRules($aRules);

    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @param $aMessages
     * @return $this
     */
    public function setErrorMessages($aMessages);

    /**
     * @return array
     */
    public function getErrors();


    public function addError($sFiled, $sMessage);

    /**
     * @param $aData array
     * @return $this
     */
    public function setData($aData);

}