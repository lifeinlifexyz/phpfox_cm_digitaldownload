<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;

class BooleanType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/boolean.html',
    ];

    protected $aColumnDefinitions = [
        [
            'type' => 'tinyint(1)',
            'default' => 'DEFAULT \'0\'',
        ]
    ];

    public function getDisplay()
    {
        return (isset($this['value']) && $this['value']) ? _p('Yes') : _p('No');
    }
}