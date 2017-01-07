<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;

class StringType extends AbstractType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/string.html',
    ];

    public function setCondition(\Phpfox_Search &$oSearch, $aSearch)
    {
        $sKey = $this->aInfo['column'];
        $sTAlias = $this->aInfo['table_alias'];
        if (($sValue = $oSearch->get($sKey)) || (isset($aSearch[$sKey]) && $sValue = $aSearch[$sKey])) {
            $oSearch->setCondition('AND `' . $sTAlias . '`.`' . $sKey . '` LIKE \'%' . $sValue . '%\'');
        }
    }
}