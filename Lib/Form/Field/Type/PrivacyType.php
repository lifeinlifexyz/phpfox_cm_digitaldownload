<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

class PrivacyType extends StringType
{
    protected $aColumnDefinitions = [
        [
            'type' => '	tinyint(1)',
            'null' => 'NULL',
        ]
    ];

    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/privacy.html',
    ];

    public function __construct(array $aData)
    {
        $aData['privacy_info'] = isset($aData['privacy_info'])
            ? $aData['privacy_info']
            : _p('Control who can see this item.');

        parent::__construct($aData);
    }

    public function getValue()
    {
        if (\Phpfox::isModule('privacy')) {
            $aVal = request()->get('val');
            return isset($aVal[$this->aInfo['name']]) ? $aVal[$this->aInfo['name']] : 0;
        } else {
            return 0;
        }
    }

    public function getFilter($sTableAlias)
    {
        $aInfo = $this->aInfo;
        return null;
    }
}