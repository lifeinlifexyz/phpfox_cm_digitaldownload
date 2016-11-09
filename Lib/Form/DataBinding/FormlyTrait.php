<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;

use Apps\CM_DigitalDownload\Lib\Form\Builder;
use Apps\CM_DigitalDownload\Lib\Form\Validator\Validator;
use Phpfox;

trait FormlyTrait
{
    /**
     * @var Builder
     */
    private $_oFormBuilder = null;
    protected $mKey = null;

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        return $this->aFieldsInfo;
    }

    /**
     * return primary key name(id)
     * @return string
     */
    public function getKeyName()
    {
        return $this->sKeyName;
    }

    /**
     * return id value. It is new object if null
     * @return string | int | null
     */
    public function getKey()
    {
        return $this->mKey;
    }

    /**
     * @param $mKey - string | int
     * @return $this
     */
    public function setKey($mKey)
    {
        $this->mKey = $mKey;
        return $this;
    }


    /**
     * return table name
     * @return string
     */
    public function getTableName()
    {
        return Phpfox::getT($this->_sTable);
    }

    public function getForm(array $aFormData = [])
    {
        $oBuilder = $this->getFormBuilder();
        $aFields = $this->getFieldsInfo();
        $mKey  = $this->getKey();

        if (!is_null($mKey)) {
            $aValues = $this->database()
                ->select('*')
                ->from($this->getTableName())
                ->where($this->getKeyName()  . ' = ' . $mKey)
                ->execute('getRow');

            foreach($aValues as $sFieldName => &$mValue) {
                if (isset($aFields[$sFieldName])) {
                    $aFields[$sFieldName]['value'] = $mValue;
                }
            }

        }
        /**
         * @var $oForm Form
         */
        $oForm = $oBuilder->build($aFields, $aFormData, true);
        $oForm->setDatabase($this->database())
            ->setKey($this->getKey())
            ->setTable($this->getTableName())
            ->setKeyName($this->getKeyName());
        return $oForm;
    }

    /**
     * @return Builder
     */
    protected function getFormBuilder()
    {
        if (is_null($this->_oFormBuilder)) {
            $this->_oFormBuilder = new Builder(request(), \Core\Controller::$__view, new Validator());
        }
        return $this->_oFormBuilder;
    }

    /**
     * @param Builder $oFormBuilder
     */
    public function setFormBuilder($oFormBuilder)
    {
        $this->_oFormBuilder = $oFormBuilder;
    }
}