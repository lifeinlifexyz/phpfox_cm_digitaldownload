<?php

namespace Apps\CM_DigitalDownload\Lib\Form;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FilterForm;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form;
use Apps\CM_DigitalDownload\Lib\Form\Validator\IValidator;
use Core\Request;
use Core\View;
use \Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form as BindedForm;
use Phpfox_Template;

class Builder
{
    private static $instance = null;
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


    private function __construct(Request $oRequest, View $oView, IValidator $oValidator)
    {
        $this->oRequest = $oRequest;
        $this->oView = $oView;
        $this->oValidator = $oValidator;
        $this->oView->env()->addFunction(new \Twig_SimpleFunction('isModule', function($sModule){
            return \Phpfox::isModule($sModule);
        }));

        $this->oView->env()->addFunction(new \Twig_SimpleFunction('privacy_field', function($sName, $sInfo, $iValue){
            Phpfox_Template::instance()->assign('aForms', [
                $sName => $iValue
            ]);
            \Phpfox::getBlock('privacy.form', [
                'privacy_name' => $sName,
                'privacy_info' => $sInfo,
            ]);
            return '';
        }));
    }

    /**
     * @return null
     */
    public static function getInstance(Request $oRequest, View $oView, IValidator $oValidator)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($oRequest, $oView, $oValidator);
        }
        return self::$instance;
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

        if (isset($aFormData['form_id'])) {
            (($sPlugin = \Phpfox_Plugin::get('digitaldownload.form_created_' . $aFormData['form_id'])) ? eval($sPlugin) : false);
        }

        (($sPlugin = \Phpfox_Plugin::get('digitaldownload.form_created')) ? eval($sPlugin) : false);

        foreach ($aFields as $aField) {
            $sType = $aField['type'];
            $oForm->addField($sType, $aField);
            $mValue = (!is_null($this->oRequest->get($aField['name'], null)))
                ? $this->oRequest->get($aField['name'])
                : (isset($aField['value']) ? $aField['value'] : null);

            $oForm->setFieldValue($aField['name'], $mValue);
        }

        return $oForm;
    }

    public function buildFilterForm(array $aFields, array $aFormData = [])
    {
        $oForm = new  FilterForm($this->oView, $aFormData);
        if (isset($aFormData['form_id'])) {
            (($sPlugin = \Phpfox_Plugin::get('digitaldownload.form_created_' . $aFormData['form_id'])) ? eval($sPlugin) : false);
        }

        (($sPlugin = \Phpfox_Plugin::get('digitaldownload.form_created')) ? eval($sPlugin) : false);

        $aSearch = $this->oRequest->getArray('search');
        foreach ($aFields as $aField) {
            $sType = $aField['type'];
            $oForm->addField($sType, $aField);
            $mValue = (isset($aSearch[$aField['name']]))
                ? $aSearch[$aField['name']]
                : (isset($aField['value']) ? $aField['value'] : null);
            $oForm->setFieldValue($aField['name'], $mValue);
        }
        return $oForm;
    }
}