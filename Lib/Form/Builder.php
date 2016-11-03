<?php

namespace Apps\CM_DigitalDownload\Lib\Form;

use Apps\CM_DigitalDownload\Lib\Form\Validator\IValidator;
use Core\Request;
use Core\View;

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

    private $_sFormTemplateDir = 'View/Form';


    public function __construct(Request $oRequest, View $oView, IValidator $oValidator)
    {
        $this->oRequest = $oRequest;
        $this->oView = $oView;
        $this->oValidator = $oValidator;
    }

    /**
     * @param View $oView
     * @param array $aFields - fields data
     * @return Form
     */
    public function build(array $aFields, array $aFormData = [])
    {
        $oForm = new Form($this->oView, $aFormData);
        $oForm->setValidator($this->oValidator);
        foreach ($aFields as $aField)
        {
            $sType = $aField['type'];
            $oForm->addField($sType, $aField);
            $mValue = ($this->oRequest->get($aField['name']))
                ? $this->oRequest->get($aField['name'])
                : (isset($aField['value']) ? $aField['value'] : null);

            $oForm->setFieldValue($aField['name'], $mValue);
        }

        return $oForm;
    }

    /**
     * @param string $sFormTemplateDir
     * @return  $this
     */
    public function setFormTemplateDir($sFormTemplateDir)
    {
        $this->_sFormTemplateDir = $sFormTemplateDir;
        return $this;
    }
}