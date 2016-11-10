<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;

class BooleanType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/boolean.html',
    ];

}