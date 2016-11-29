<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;

class StaticType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/static.html',
    ];

    public function getFilter($sTableAlias)
    {
        $aInfo = $this->aInfo;
        return null;
    }
}