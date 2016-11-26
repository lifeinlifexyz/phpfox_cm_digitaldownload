<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;

class FileType extends AbstractType
{
    protected $oFile;
    protected $sDir;
    protected $aSupported =  [];
    protected $iMaxSize =  null;

    public function __construct(array $aData)
    {
        parent::__construct($aData);
        $this->oFile = isset($aData['oFile'])
            ? $aData['oFile']
            : \Phpfox_File::instance();

        $this->sDir = isset($aData['dir']) ? $aData['dir'] : PHPFOX_DIR_FILE . 'form_files' . PHPFOX_DS;
        if (!is_dir($this->sDir)) {
            $this->oFile->mkdir($this->sDir, true);
        }

        if (isset($this->aInfo['supported'])) {
            $this->aSupported = $this->aInfo['supported'];
        }
    }

    protected $aColumnDefinitions = [
        [
            'type' => '	tinyint(1)',
            'null' => 'NULL',
        ],
        [
            'field' => 'server_id',
            'type' => '	tinyint(1)',
            'null' => 'NULL',
        ]
    ];

    protected $aInfo  = [
        'template' => '@CM_DigitalDownload/form/fields/file.html',
    ];

    public function isValid()
    {
        if ($this->hasFile()) {
            $this->oFile->load($this->aInfo['name'] . '_file', $this->aSupported);
            $sFile = $this->oFile->upload($this->aInfo['name'] . '_file', $this->sDir,
                $_FILES[$this->aInfo['name'] . '_file']['name']);
            $this->aInfo['value'] =  sprintf($sFile, '');
        }
        return parent::isValid();
    }

    protected function hasFile()
    {
        return isset($_FILES[$this->aInfo['name'] . '_file']['name'])
            && ($_FILES[$this->aInfo['name'] . '_file']['name'] != '');
    }

    public function isEmpty(){
        return !$this->hasFile();
    }
}