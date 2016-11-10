<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field;


class MultiLangType extends AbstractType
{

    /**
     * @var \Language_Service_Language
     */
    protected $oLang;
    public function __construct(array $aData)
    {
        $this->oLang = &\Language_Service_Language::instance();
        parent::__construct($aData);
        $this->aInfo['aLanguages'] = $this->oLang->getAll();
    }

    public function getValue()
    {
        $sPhrase = parent::getValue();
        $sName = $this->aInfo['name'];
        $aLanguages =  $this->aInfo['aLanguages'];
        $sModule = isset($this->aInfo['module']) ? $this->aInfo['module'] : 'core';

        if (is_null($sPhrase)) {

            //insert phrase
            $sPhrase = $sName . '_multi_lang_string_' . md5($sName . PHPFOX_TIME . rand(1, 100));
            $aText = [];
            foreach ($aLanguages as $aLanguage){
                $aText[$aLanguage['language_id']] = request()->get($sName . '_' . $aLanguage['language_id']);
            }

            $aValsPhrase = [
                'product_id' => 'phpfox',
                'module' => $sModule . '|' . $sModule,
                'var_name' => $sPhrase,
                'text' => $aText
            ];
            $aVals['name'] = \Language_Service_Phrase_Process::instance()->add($aValsPhrase);

        } elseif (!is_null($sPhrase) && \Phpfox::isPhrase($sPhrase)) {

            //update phrase
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']])){
                    $sText = request()->get($sName . '_' . $aLanguage['language_id']);
                    \Language_Service_Phrase_Process::instance()->updateVarName($aLanguage['language_id'], $sPhrase, $sText);
                }
            }

        }

        return $sPhrase;
    }
}