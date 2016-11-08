<?php
namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


use Phpfox_Service;

class Form extends \Apps\CM_DigitalDownload\Lib\Form\Form implements IForm
{
    /**
     * @var IDataInfo
     */
    protected $oDataInfo = null;

    /**
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        $mKey = $this->oDataInfo->getKey();
        $sTable = $this->oDataInfo->getTableName();
        $aValues = $this->getFieldsValue();
        //todo:: set before and after save events;
        if (is_null($mKey)) {
            $bRes = $this->oDataInfo->database()->insert($sTable, $aValues);
        } else {
            $bRes = $this->oDataInfo->database()->update($sTable, $aValues, $this->oDataInfo->getKeyName() . ' = ' . $mKey);
        }

        if (!$bRes) {
            throw new \Exception("Unable to save object to \" {$sTable} \" ");
        }

        return $this;
    }

    /**
     * @param IDataInfo $oDataInfo
     * @return Form
     */
    public function setDataInfo($oDataInfo)
    {

        if (!($oDataInfo instanceof IDataInfo && $oDataInfo instanceof Phpfox_Service)) {
            throw new \InvalidArgumentException('$oDataInfo must be instanceof "IDataInfo" and "Phpfox_Service"');
        }

        $this->oDataInfo = $oDataInfo;
        return $this;
    }
}