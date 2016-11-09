<?php
namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


use Core\View;

class Form extends \Apps\CM_DigitalDownload\Lib\Form\Form implements IForm
{
    protected $mKey = null;
    protected $sKeyName = null;
    protected $sTable = null;
    protected $oDatabase = null;

    public function __construct(View $oView, \Phpfox_Database $oDataBase = null, array $aData, $sTable = null, $mKey = null, $sKeyName = 'id')
    {
        $this->mKey = $mKey;
        $this->sKeyName = $sKeyName;
        $this->sTable = $sTable;
        $this->oDatabase = $oDataBase;
        parent::__construct($oView, $aData);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        $aValues = $this->getFieldsValue();
        //todo:: set before and after save events;
        if (is_null($this->mKey)) {
            $bRes = $this->oDatabase->insert($this->sTable, $aValues);
        } else {
            $bRes = $this->oDatabase->update($this->sTable, $aValues, $this->sKeyName . ' = ' . $this->mKey);
        }

        if (!$bRes) {
            throw new \Exception("Unable to save object to \" {$this->sTable} \" ");
        }

        return $this;
    }

    /**
     * @param null $mKey
     * @return Form
     */
    public function setKey($mKey)
    {
        $this->mKey = $mKey;
        return $this;
    }

    /**
     * @param null $sKeyName
     * @return Form
     */
    public function setKeyName($sKeyName)
    {
        $this->sKeyName = $sKeyName;
        return $this;
    }

    /**
     * @param null $sTable
     * @return Form
     */
    public function setTable($sTable)
    {
        $this->sTable = $sTable;
        return $this;
    }

    /**
     * @param \Phpfox_Database $oDatabase
     * @return Form
     */
    public function setDatabase($oDatabase)
    {
        $this->oDatabase = $oDatabase;
        return $this;
    }

}