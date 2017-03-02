<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Phpfox;

class UrlType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/url.html',
        'rules' => 'url',
    ];

    public function setCondition(\Phpfox_Search &$oSearch, $aSearch)
    {
        return false;
    }
}