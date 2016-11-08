<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


use Phpfox;

trait DataInfoTrait
{

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
}