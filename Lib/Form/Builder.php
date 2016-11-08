<?php

namespace Apps\CM_DigitalDownload\Lib\Form;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IDataBinder;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IForm;
use Apps\CM_DigitalDownload\Lib\Form\Validator\IValidator;
use Core\Request;
use Core\View;

class Builder implements IDataBinder
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
     * @return Form
     */
    public function build(array $aFields, array $aFormData = [])
    {
        $oForm = new Form($this->oView, $aFormData);
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

    /**
     * @param string $mService
     * @param null|string|int $mKey
     * @return IForm
     */
    public function getBindedForm($mService, $mKey = null, array $aFormData = [], array $aServiceParams = [])
    {

        $oService =  is_string($mService)
            ? \Phpfox::getService($mService, $aServiceParams)
            : $mService;

        $aFields = $oService->getFieldsInfo();
        //todo:: check data info instanse;
        if (!is_null($mKey)) {
            $oService->setKey($mKey);
            $aValues = \Phpfox_Database::instance()
                ->select('*')
                ->from($oService->getTableName())
                ->where($oService->getKeyName()  . ' = ' . $mKey)
                ->execute('getRow');
            foreach($aValues as $sFieldName => &$mValue) {
                if (isset($aFields[$sFieldName])) {
                    $aFields[$sFieldName]['value'] = $mValue;
                }
            }
        }
        $oForm = $this->build($aFields, $aFormData);
        $oForm->setDataInfo($oService);

        return $oForm;

    }
}