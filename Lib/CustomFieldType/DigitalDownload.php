<?php

namespace Apps\CM_DigitalDownload\Lib\CustomFieldType;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Form\Field\Type\FileType;
use Apps\CM_DigitalDownload\Lib\Form\Field\Type\PriceType;
use Phpfox;

class DigitalDownload extends AbstractType
{
    /**
     * @var \Phpfox_File
     */
    protected $oFile;

    protected $sCurrency;
    protected $aSupported =  [];
    protected $sDir;
    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/custom_fields/digital-download.html',
        'display_template' => '@CM_DigitalDownload/form/custom_fields/display/digital-download.html',
        'is_search' => false,
    ];

    protected $aColumnDefinitions = [
        [
            'type' => 'VARCHAR(250)',
            'null' => 'NULL',
        ],
        [
            'field' => 'server_id',
            'type' => '	tinyint(1)',
            'null' => 'NULL',
        ],
        [
            'field' => 'limit',
            'type' => '	int(11)',
            'null' => 'NULL',
        ],
        [
            'type' => 'decimal(14,2)',
            'null' => 'NULL',
            'field' => 'price',
        ],
        [
            'type' => 'char(3)',
            'null' => 'NULL',
            'field' => 'currency_id',
        ]
    ];
    public function __construct(array $aData)
    {
        parent::__construct($aData);

        $aCurrencies = Phpfox::getService('core.currency')->get();
        foreach ($aCurrencies as $iKey => $aCurrency) {
            $aCurrencies[$iKey]['is_default'] = '0';

            if (Phpfox::getService('core.currency')->getDefault() == $iKey) {
                $aCurrencies[$iKey]['is_default'] = '1';
            }
        }
        $this->aInfo['aCurrencies'] = $aCurrencies;
        $this->aInfo['multi_column'] = true;

        $this->oFile = isset($aData['oFile'])
            ? $aData['oFile']
            : \Phpfox_File::instance();

        $this->sDir = isset($aData['dir']) ? $aData['dir'] : PHPFOX_DIR_FILE . $aData['name'] . '_dd_files' . PHPFOX_DS;
        if (!is_dir($this->sDir)) {
            $this->oFile->mkdir($this->sDir, true);
        }

        if (isset($this->aInfo['supported'])) {
            $this->aSupported = $this->aInfo['supported'];
        }


    }

    public function setMValue($aRow)
    {
        $this->sCurrency = $aRow['price_currency_id'];
        $this->aInfo['value'] = $aRow[$this->aInfo['name']];
    }

    public function getValue()
    {
        return [

            $this->aInfo['name'] => isset($this->aInfo['value']['file'])
                ? $this->aInfo['value']['file']
                : $this->aInfo['row_value'][$this->aInfo['name']],

            $this->aInfo['name'] . '_price' => isset($this->aInfo['value']['price'])
                ? $this->aInfo['value']['price']
                : $this->aInfo['row_value'][$this->aInfo['name'] . '_price'],
            $this->aInfo['name'] . '_currency_id' => isset($this->aInfo['value']['currency_id'])
                ? $this->aInfo['value']['currency_id']
                : $this->aInfo['row_value'][$this->aInfo['name'] . '_currency_id'],
            $this->aInfo['name'] . '_limit' => isset($this->aInfo['value']['limit'])
                ? $this->aInfo['value']['limit']
                : $this->aInfo['row_value'][$this->aInfo['name'] . '_limit'],
        ];
    }

    protected function getVars()
    {
        $this->aInfo['value_currency_id'] = isset($this->aInfo['value']['currency_id'])
            ? $this->aInfo['value']['currency_id']
            : (isset($this->aInfo['row_value'][$this->aInfo['name'] . '_currency_id']) ? $this->aInfo['row_value'][$this->aInfo['name'] . '_currency_id']: null);

        $this->aInfo['value_price'] = isset($this->aInfo['value']['price'])
            ? $this->aInfo['value']['price']
            : (isset($this->aInfo['row_value'][$this->aInfo['name'] . '_price']) ? $this->aInfo['row_value'][$this->aInfo['name'] . '_price']: null);

        $this->aInfo['value_limit'] = isset($this->aInfo['value']['limit'])
            ? $this->aInfo['value']['limit']
            : (isset($this->aInfo['row_value'][$this->aInfo['name'] . '_limit']) ? $this->aInfo['row_value'][$this->aInfo['name'] . '_limit']: null);

        $this->aInfo['value'] = isset($this->aInfo['value']) ? $this->aInfo['value'] : 0;

        $this->aInfo['required'] = isset($this->aInfo['rules']) && (strpos($this->aInfo['rules'], 'required') !== false);

        return $this->aInfo;
    }

    public function isValid()
    {
        if ($this->hasFile()) {
            $this->oFile->load($this->aInfo['name'] . '_file', $this->aSupported);
            $sFile = $this->oFile->upload($this->aInfo['name'] . '_file', $this->sDir,
                $_FILES[$this->aInfo['name'] . '_file']['name']);
            $this->aInfo['value']['file'] =  sprintf($sFile, '');
        }
        return parent::isValid();
    }

    protected function hasFile()
    {
        return isset($_FILES[$this->aInfo['name'] . '_file']['name'])
        && ($_FILES[$this->aInfo['name'] . '_file']['name'] != '');
    }

    public function isEmpty(){
        if (!empty($this->aInfo['value']['file'])) {
            return false;
        }
        return !$this->hasFile();
    }

    private function isFree()
    {
        return $this->aInfo['row_value'][$this->aInfo['name'] . '_price'] == '0.00';
    }

    private function canDownload()
    {
        return $this->isFree();
    }

    public function getDisplay()
    {
        $aVars = $this->aInfo['row_value'];
        $aVars['is_free'] = $this->isFree();
        $aVars['can_download'] = $this->canDownload();
        $sPrice = $this->aInfo['row_value'][$this->aInfo['name'] . '_price'];

        $aVars['price'] = Phpfox::getService('core.currency')->getCurrency(number_format($sPrice, 2),
            $this->aInfo['row_value'][$this->aInfo['name'] . '_currency_id']);
        $aVars['dd_name'] = $this->aInfo['name'];

        return $this->oView->view($this->aInfo['display_template'], $aVars);
    }

}