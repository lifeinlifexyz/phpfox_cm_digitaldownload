<?php
namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Core\Event;
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
        $aData['form_id'] = isset($aData['form_id']) ? $aData['form_id'] : md5(microtime() . rand(1, 100));
        parent::__construct($oView, $aData);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function save()
    {
        $aValues = $this->getFieldsValue();

        (($sPlugin = \Phpfox_Plugin::get('before_save_' . $this->sTable )) ? eval($sPlugin) : false);

        if (is_null($this->mKey)) {
            $iId = $this->oDatabase->insert($this->sTable, $aValues);
        } else {
            $iId = $this->oDatabase->update($this->sTable, $aValues, $this->sKeyName . ' = ' . $this->mKey);
        }

        if (!$iId) {
            throw new \Exception("Unable to save object to \" {$this->sTable} \" ");
        }

        (($sPlugin = \Phpfox_Plugin::get('after_save_' . $this->sTable )) ? eval($sPlugin) : false);

        return $iId;
    }

    /**
     * @param string $sTemplate
     * @return string
     */
    public function render($sTemplate = '@CM_DigitalDownload/form/binded-form.html')
    {
        $this->aData['saveCaption'] = is_null($this->mKey) ? _p('Add') : _p('Save');
        return parent::render($sTemplate);
    }

    /**
     * @return array
     */
    public function getFieldsValue()
    {
        $aResult = [];
        foreach ($this->aFields as $sField => &$oField) {
            /**
             * @var $oField AbstractType
             */
            $aFieldsInfo = $oField->getInfo();
            if (isset($aFieldsInfo['multi_column']) && $aFieldsInfo['multi_column']) {
                $aValues = $oField->getValue();
                foreach($aValues as $sName => $mValue) {
                    if (isset($aFieldsInfo['filter'][$sName])) {
                        $aResult[$sName] = call_user_func($aFieldsInfo['filter'][$sName], $mValue);
                    } else {
                        $aResult[$sName] = $mValue;
                    }
                }
            } elseif (isset($aFieldsInfo['filter'])) {
                $aResult[$sField] = call_user_func($aFieldsInfo['filter'], $oField->getValue());
            } else {
                $aResult[$sField] = $oField->getValue();
            }
        }
        return $aResult;
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