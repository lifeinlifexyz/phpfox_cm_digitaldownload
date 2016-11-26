<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Phpfox;

class PriceType extends AbstractType
{

    protected $aColumnDefinitions = [
        [
            'type' => 'decimal(14,2)',
            'null' => 'NULL',
        ],
        [
            'type' => 'char(3)',
            'null' => 'NULL',
            'field' => 'currency_id',
        ]
    ];

    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/price.html',
    ];

    public function __construct(array $aData)
    {
        parent::__construct($aData);

        $aCurrencies = Phpfox::getService('core.currency')->get();
        foreach ($aCurrencies as $iKey => $aCurrency)
        {
            $aCurrencies[$iKey]['is_default'] = '0';

            if (Phpfox::getService('core.currency')->getDefault() == $iKey)
            {
                $aCurrencies[$iKey]['is_default'] = '1';
            }
        }
        $this->aInfo['aCurrencies'] = $aCurrencies;
        $this->aInfo['multi_column'] = true;

    }

    public function getValue()
    {
        return [
            $this->aInfo['name'] => parent::getValue(),
            $this->aInfo['name'] . '_currency_id' => request()->get($this->aInfo['name'] . '_currency_id'),
        ];
    }

}