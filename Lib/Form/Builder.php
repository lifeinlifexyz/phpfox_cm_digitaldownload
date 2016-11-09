<?php

namespace Apps\CM_DigitalDownload\Lib\Form;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form;
use Apps\CM_DigitalDownload\Lib\Form\Validator\IValidator;
use Core\Request;
use Core\View;
use \Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form as BindedForm;

class Builder
{
    /**
     * @var Request
     */
    protected $oRequest;
    /**
     * @var View
     */
    protected $oView;
    /**
     * @var IValidator
     */
    protected $oValidator;


    public function __construct(Request $oRequest, View $oView, IValidator $oValidator)
    {
        $this->oRequest = $oRequest;
        $this->oView = $oView;
        $this->oValidator = $oValidator;
    }

    /**
     * @param array $aFields
     * @param array $aFormData
     * @return Form | BindedForm
     */
    public function build(array $aFields, array $aFormData = [], $bDataBinded = false)
    {
        $oForm = (!$bDataBinded)
        ? (new Form($this->oView, $aFormData))
        : (new  BindedForm($this->oView, null, $aFormData));
        $oForm->setValidator($this->oValidator);
        foreach ($aFields as $aField) {
            $sType = $aField['type'];
            $oForm->addField($sType, $aField);
            $mValue = ($this->oRequest->get($aField['name']))
                ? $this->oRequest->get($aField['name'])
                : (isset($aField['value']) ? $aField['value'] : null);

            $oForm->setFieldValue($aField['name'], $mValue);
        }

        return $oForm;
    }
}