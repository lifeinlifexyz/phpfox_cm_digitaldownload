<?php

namespace Apps\CM_DigitalDownload\Service;


class Display extends \Apps\CM_DigitalDownload\Lib\Form\DataBinding\Display
{
    private $oDD;
    protected $sTitleSettings = '$title';

    public function __construct(DigitalDownload $oDD)
    {
        $this->oDD = $oDD;
        parent::__construct();
    }

    /**
     * @param array $aRow
     * @return Display
     */
    public function setRow($aRow)
    {
        $this->aRow = $aRow;
        $this->oForm = $this->oDD->setCategoryId($aRow['category_id'])->getForm();
        $this->oForm->addField('tree', $this->oDD->getCategoryFieldData());
        $this->oForm['category_id']->setValue($aRow['category_id']);
        $aCatInfo = $this->oForm['category_id']->getValueArray();
        $this->sTitleSettings = $aCatInfo['title'];
        return $this;
    }

    public function getFields(array $aExclude = [])
    {
        $aFields = $this->oForm->getFields();
        if (count($aExclude) > 0) {
            $aFields = array_diff_key($aFields, $aExclude);
        }
        return $aFields;
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
            case 'seo_keyword':
                $aCatInfo = $this->oForm['category_id']->getValueArray();
                return $this->parseVars($aCatInfo['keywords']);
                break;
            case 'seo_description':
                $aCatInfo = $this->oForm['category_id']->getValueArray();
                return $this->parseVars($aCatInfo['description']);
                break;
            case 'category':
                return $this->oForm['category_id']->getDisplay();
                break;
            default:
                return parent::offsetGet($offset);
        }
    }

    public function __toString()
    {
        return $this->parseVars($this->sTitleSettings);
    }
}