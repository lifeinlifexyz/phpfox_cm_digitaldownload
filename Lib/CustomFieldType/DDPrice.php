<?php

namespace Apps\CM_DigitalDownload\Lib\CustomFieldType;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Form\Field\Type\PriceType;

class DDPrice extends PriceType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/price.html',
        'is_search' => false,
        'min' => 0,
        'max' => 100000000,
    ];


    public function setCondition(\Phpfox_Search &$oSearch, $aSearch)
    {
        $sKey = $this->aInfo['column'];
        $sTAlias = $this->aInfo['table_alias'];
        $aColumns = $this->aInfo['columns'];
        if ((($aValue = $oSearch->get($sKey)) || (isset($aSearch[$sKey]) && $aValue = $aSearch[$sKey]))) {
            $iMin = (int)(!isset($aValue['min']) ? $aValue[0] : $aValue['min']);
            $iMax = (int)(!isset($aValue['max']) ? $aValue[1] : $aValue['max']);
            $this->aInfo['value']['min'] =  $iMin;
            $this->aInfo['value']['max'] =  $iMax;

            foreach($aColumns as &$sColumn) {
                $oSearch->setCondition('AND (`'
                    . $sTAlias . '`.`' . $sColumn . '_price` >= ' . $iMin
                    . ' AND `' . $sTAlias . '`.`' . $sColumn . '_price` <= ' . $iMax
                    . ')');
            }
        }
    }

}