<?php

namespace Apps\CM_DigitalDownload\Lib\CustomFieldType;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Form\Field\Type\PriceType;

class PlanOption extends PriceType
{
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/custom_fields/plan-option.html',
        'is_search' => false,
    ];

    public function getValue()
    {
        return [

            $this->aInfo['name'] => isset($this->aInfo['value']['price'])
                ? $this->aInfo['value']['price']
                : $this->aInfo['row_value'][$this->aInfo['name']],

            $this->aInfo['name'] . '_allowed' => isset($this->aInfo['value']['allowed'])
                ? $this->aInfo['value']['allowed']
                : $this->aInfo['row_value'][$this->aInfo['name'] . '_allowed'],
        ];
    }

    protected function getVars()
    {
        $this->aInfo['value_allowed'] = isset($this->aInfo['value']['allowed'])
            ? $this->aInfo['value']['allowed']
            : $this->aInfo['row_value'][$this->aInfo['name'] . '_allowed'];

        $this->aInfo['value'] = isset($this->aInfo['value']) ? $this->aInfo['value'] : 0;

        $this->aInfo['required'] = isset($this->aInfo['rules']) && (strpos($this->aInfo['rules'], 'required') !== false);

        return $this->aInfo;
    }

    public function setMValue($aRow)
    {
        $this->sCurrency = $aRow['price_currency_id'];
        $this->aInfo['value'] = $aRow[$this->aInfo['name']];
    }

    public function getDisplay()
    {
        return '<p>'.
                    $this->aInfo['title'] . ': <strong>' .
                    (($this->aInfo['value'] != '0.00')
                    ? \Phpfox::getService('core.currency')->getSymbol($this->sCurrency)
                        . ' ' . number_format($this->aInfo['value'], 2)
                    : _p('Free')).
                '</strong></p>';
    }
}