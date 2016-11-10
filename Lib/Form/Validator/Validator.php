<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Validator;


class Validator implements IValidator
{
    protected $aRules = [];
    protected $aErrorMessages = [];
    protected $aErrors = [];
    protected $aData = [];
    protected $aRuleValidator = [];

    protected $aKnownRules = [
        'required' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\Required@validate',
        'email' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\Email@validate',
        'min' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\Min@validate',
        'max' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\Max@validate',
        'minLength' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\MinLength@validate',
        'maxLength' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\MaxLength@validate',
        'length' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\Length@validate',
        'in' => 'Apps\CM_DigitalDownload\Lib\Form\Validator\Rule\In@validate',
    ];

    /**
     * @param $aRules array
     * @return $this
     */
    public function setRules($aRules)
    {
        $this->aRules = $aRules;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        $bResult = true;

        foreach ($this->aRules as $sField => &$aRule) {
            $this->validate($sField, $aRule);
            if (isset($this->aErrors[$sField])) {
                $bResult = false;
            }
        }
        return $bResult;
    }

    /**
     * @param $aMessages
     * @return $this
     */
    public function setErrorMessages($aMessages)
    {
        $this->aErrorMessages = $aMessages;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }

    /**
     * @param $aData array
     * @return $this
     */
    public function setData($aData)
    {
        $this->aData = $aData;
        return $this;
    }

    /**
     * @param $sRuleName string
     * @param $sCallback string
     * @return $this
     */
    public function extend($sRuleName, $sCallback)
    {
        $this->aKnownRules[$sRuleName] = $sCallback;
        return $this;
    }

    /**
     * @param $sField string
     * @param $aRule array
     */
    protected function validate($sField, $aRule)
    {
        $aParams = [];
        foreach ($aRule as &$sRule) {
            if (strpos($sRule, ':')) {
                $aParams = explode(':', $sRule);
                $sRuleName = array_pop($aParams);

            } else {
                $sRuleName = $sRule;
            }
            call_user_func_array($this->getCallback($sRuleName, $sField),
                array_merge([$sField, $this->aData[$sField]], $aParams));
        }
    }


    protected function getCallback($sRuleName, $sField)
    {
        if (!isset($this->aRuleValidator[$sRuleName])) {
            if (!isset($this->aKnownRules[$sRuleName])) {
                throw new UnknownRule('Unknown "' . $sRuleName . '" validator rule');
            }
            $aRule = explode('@', $this->aKnownRules[$sRuleName]);
            $sClassName = $aRule[0];
            $oRule = new $sClassName($this);
            $this->aRuleValidator[$sRuleName] = [$oRule, $aRule[1]];
        }

        if (isset($this->aErrorMessages[$sField . '.' . $sRuleName])) {
            $this->aRuleValidator[$sRuleName][0]->setErrorMessage($this->aErrorMessages[$sField . '.' . $sRuleName]);
        }

        return $this->aRuleValidator[$sRuleName];
    }


    public function addError($sFiled, $sMessage)
    {
        //todo:: if need translate we can translate here
        $this->aErrors[$sFiled][] = $sMessage;
    }
}