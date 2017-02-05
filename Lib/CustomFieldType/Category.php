<?php

namespace Apps\CM_DigitalDownload\Lib\CustomFieldType;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Form\Field\Type\TreeType;

class Category extends TreeType
{

    public function getDisplay()
    {
        $iValue = $this->getValue();
        if (is_null($iValue)) {
            return '';
        }

        $aBranches = $this->oTree->parents($this->aInfo['items'], $iValue);
        $sTitleField = $this->aInfo['title_field'];
        $sSeperator = '&#10137;';

        $sRes = '';
        $sName = $this->aInfo['name'];
        $sBaseModuleUrl = \Phpfox_Url::instance()->makeUrl('digitaldownload');
        foreach($aBranches as &$aItem) {
            $sRes .= '<a href="' . $sBaseModuleUrl. '?search[' . $sName . ']=' . $aItem[$sName] . '" >'
                        . _p($aItem[$sTitleField])
                    . '</a>' . $sSeperator;
        }
        return rtrim($sRes, $sSeperator);
    }
}