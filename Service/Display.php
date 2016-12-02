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
        switch($offset) {
            case 'images':
                return !empty($this->aRow['images']) ? json_decode($this->aRow['images'], true) : [];
                break;
            case 'main_image':
                $aImgs = $this->offsetGet('images');
                $aImg =  array_shift($aImgs);
                $aImg['server_id'] = isset($aImg['server_id']) ? $aImg['server_id'] : null;
                $aImg['image_path'] = isset($aImg['image_path']) ? $aImg['image_path'] : null;
                return $aImg;
                break;
            case 'url':
                $iId = $this->aRow['id'];
                return  \Phpfox::getLib('url')->makeUrl('digitaldownload.' . $iId);
                break;
            default:
                return parent::offsetGet($offset);
        }
    }
}