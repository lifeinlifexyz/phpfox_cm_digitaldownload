<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\MultiLangType;

class MultiLangStringType extends MultiLangType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/multi-lang-string.html',
    ];
}