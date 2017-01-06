<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Core\Event;
use Phpfox;

class IntegerType extends AbstractType
{
    protected $sCurrency = '';

    protected $aColumnDefinitions = [
        [
            'type' => 'INT(11)',
            'null' => 'NULL',
        ],
    ];

    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/integer.html',
        'is_search' => false,
        'min' => 0,
    ];

    protected function getVars()
    {
        if ($this->aInfo['is_search'] === true) {

            if (!isset($this->aInfo['max'])) {
                $sColName = $this->aInfo['column'];
                $aCond = [];
                if (is_array($aExtCond = Event::trigger('before_get_min_max_' . $sColName, $aCond))) {
                    $aCond = $aExtCond;
                }
                $aRes  = db()->select('MIN(`' . $sColName . '`) as `min`, MAX(`' . $sColName . '`) as `max`')
                    ->from($this->aInfo['table'])
                    ->where($aCond)
                    ->execute('getslaverow');
                $this->aInfo['min'] = round($aRes['min'], 0);
                $this->aInfo['max'] = round($aRes['max'], 0);
            }

        }
        return $this->aInfo;
    }

    public function setCondition(\Phpfox_Search &$oSearch, $aSearch)
    {
        $sKey = $this->aInfo['column'];
        $sTAlias = $this->aInfo['table_alias'];
        if (($aValue = $oSearch->get($sKey)) || (isset($aSearch[$sKey]) && $aValue = $aSearch[$sKey])) {
            $oSearch->setCondition('AND `' . $sTAlias . '`.`' . $sKey . '` >= ' . $aValue['min']);
            $oSearch->setCondition('AND `' . $sTAlias . '`.`' . $sKey . '` <= ' . $aValue['max']);
        }
    }
}