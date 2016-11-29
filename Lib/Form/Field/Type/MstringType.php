<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\MultiLangType;

class MstringType extends MultiLangType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/multi-lang-string.html',
    ];

    public function getDisplay()
    {
        return _p($this->aInfo['value']);
    }
}