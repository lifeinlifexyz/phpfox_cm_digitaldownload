<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;

class MultilistType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/multi-list.html',
    ];

    public function getValue()
    {
        $aValue = (array) $this->aInfo['value'];
        return implode(',', $aValue);
    }


    public function getVars() {
        if (!is_array($this->aInfo['value'])) {
            $this->aInfo['value'] = explode(',', $this->aInfo['value']);
        }
        return parent::getVars();
    }
}