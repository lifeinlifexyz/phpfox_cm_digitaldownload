<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field;


class MultiLangType extends AbstractType
{

    /**
     * @var \Language_Service_Language
     */
    protected $oLang;
    protected $oRequest;

    public function __construct(array $aData)
    {
        $this->oLang = &\Language_Service_Language::instance();
        $this->oRequest = &\Phpfox_Request::instance();
        parent::__construct($aData);
        $this->aInfo['aLanguages'] = $this->oLang->getAll();
    }

    public function getValue()
    {
        $sPhrase = parent::getValue();
        $sName = $this->aInfo['name'];
        $aLanguages = $this->aInfo['aLanguages'];
        $sModule = isset($this->aInfo['module']) ? $this->aInfo['module'] : 'core';
        if (is_null($sPhrase)) {
            //insert phrase
            $aText = [];
            foreach ($aLanguages as &$aLanguage) {

                if (!$this->oRequest->is($sName . '_' . $aLanguage['language_id'])) {
                    return $sPhrase;
                }

                $aText[$aLanguage['language_id']] = $this->oRequest->get($sName . '_' . $aLanguage['language_id']);
            }
            $sPhrase = $sName . '_multi_lang_string_' . md5($sName . PHPFOX_TIME . rand(1, 100));

            $aValsPhrase = [
                'product_id' => 'phpfox',
                'module' => $sModule . '|' . $sModule,
                'var_name' => $sPhrase,
                'text' => $aText
            ];
            $aVals['name'] = \Language_Service_Phrase_Process::instance()->add($aValsPhrase);
            $sPhrase = $sModule . '.' . $sPhrase;

        } elseif (!is_null($sPhrase) && \Phpfox::isPhrase($sPhrase)) {
            //update phrase
            foreach ($aLanguages as &$aLanguage) {
                if ($this->oRequest->is($sName . '_' . $aLanguage['language_id'])) {
                    $sText = $this->oRequest->get($sName . '_' . $aLanguage['language_id']);
                    \Language_Service_Phrase_Process::instance()->updateVarName($aLanguage['language_id'], $sPhrase, $sText);
                }
            }

        }

        return $sPhrase;
    }

    public function isEmpty()
    {
        $aLanguages = $this->aInfo['aLanguages'];
        $sName = $this->aInfo['name'];

        foreach ($aLanguages as $aLanguage) {
            if (!request()->is($sName . '_' . $aLanguage['language_id'])) {
                return true;
            }
        }

        return false;
    }
}