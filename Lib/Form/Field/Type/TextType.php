<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

class TextType extends StringType
{

    protected $aColumnDefinitions = [
        [
            'type' => 'TEXT (5000)',
            'null' => 'NULL',
        ]
    ];

    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/text.html',
    ];

}