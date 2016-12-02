<?php

namespace Apps\CM_DigitalDownload\Service;


class Display extends \Apps\CM_DigitalDownload\Lib\Form\DataBinding\Display
{
    private $oDD;
    public function __construct(DigitalDownload $oDD)
    {
        $this->oDD = $oDD;
        parent::__construct();
    }

    /**
     * @param mixed $aRow
     * @return Display
     */
    public function setRow($aRow)
    {
        $this->aRow = $aRow;
        $this->oForm = $this->oDD->setCategoryId($aRow['category_id'])->getForm();
        return $this;
    }

    public function getRow()
    {
        return $this->aRow;
    }

    public function offsetGet($offset)
    {
        if ($offset == 'images') {
            return !empty($this->aRow['images']) ? json_decode($this->aRow['images'], true) : [];
        } else {
            return parent::offsetGet($offset);
        }
    }
}