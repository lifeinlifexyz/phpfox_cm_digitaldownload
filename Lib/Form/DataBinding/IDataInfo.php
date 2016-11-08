<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;

interface IDataInfo
{
    /**
     * return array of fields info
     * @return array
     */
     public function getFieldsInfo();

    /**
     * return primary key name(id)
     * @return string
     */
     public function getKeyName();

    /**
     * return id value. It is new object if null
     * @return string | int | null
     */
     public function getKey();

    /**
     * @param $mKey - string | int
     * @return $this
     */
     public function setKey($mKey);

    /**
     * return table name
     * @return string
     */
    public function getTableName();
}