<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Form\Exception\RequiredArgumentException;

class HiddenType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/hidden.html',
    ];

    public function __construct(array $aData)
    {
        if (!isset($aData['value'])) {
            throw  new RequiredArgumentException('Required element "value" in argument aData');
        }
        $aData['title'] = isset($aData['title']) ? $aData['title'] : '';
        parent::__construct($aData);
    }

}