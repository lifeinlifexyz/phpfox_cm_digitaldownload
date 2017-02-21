<?php

namespace Apps\CM_DigitalDownload\Service\Digitaldownload;


use Apps\CM_DigitalDownload\Lib\Form\Exception\RequiredArgumentException;
use Phpfox;

class Display extends \Apps\CM_DigitalDownload\Lib\Form\DataBinding\Display
{
    private $oDD;
    protected $sTitleSettings = '$title';
    protected $aDDFieldNames = [];
    protected $aExtraFields = [
        'images',
        'main_image',
        'url',
        'seo_keyword',
        'seo_description',
        'category',
        'aDDPrice',
        'short_description',
        'rating',
        'full_rating',
    ];

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
        $this->oForm->addField('category', $this->oDD->getCategoryFieldData());
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
                $aImg['image_path'] = isset($aImg['image_path']) ? 'digitaldownload/' . $aImg['image_path'] : 'digitaldownload/dd_no_image.jpg';
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
            case 'aDDPrice':
                $aDDPrice = [];

                foreach($this->aDDFieldNames  as $aDDFieldName) {
                    if (!isset($this->oForm[$aDDFieldName])) {
                        continue;
                    }
                    $aDDPrice[] = [
                          'caption' => $this->oForm[$aDDFieldName]['caption'],
                          'price' => $this->aRow[$aDDFieldName . '_price'],
                          'currency_id' => $this->aRow[$aDDFieldName . '_currency_id'],
                          'limit' => $this->aRow[$aDDFieldName . '_limit'],
                    ];
                }

                return $aDDPrice;
                break;
            case 'short_description':
                return Phpfox::getLib('parse.input')->clean($this->aRow['description'], 100);
                break;
            case 'rating':
                $aRating =  Phpfox::getService('digitaldownload.rating')->getRating($this->aRow['id'], $this->aRow['rating']);
                $aRating['dd_id'] = $this->aRow['id'];
                return view('@CM_DigitalDownload/rating/rating.html', $aRating);
                break;
            case 'full_rating':
                $aRating =  Phpfox::getService('digitaldownload.rating')->getRating($this->aRow['id'], $this->aRow['rating']);
                $aRating['dd_id'] = $this->aRow['id'];
                return view('@CM_DigitalDownload/rating/full.html', $aRating);
                break;
            default:
                return parent::offsetGet($offset);
        }
    }

    public function offsetExists($offset)
    {
        return in_array($offset, $this->aExtraFields) || parent::offsetExists($offset);
    }

    public function getField($sField)
    {
        if (isset($this->oForm[$sField])) {
            $oField = $this->oForm->getField($sField);
            if (method_exists($oField, 'setMValue')) {
                $oField->setMValue($this->aRow);
            } else {
                $oField->setValue($this->aRow[$sField]);
            }
            return $oField;
        } else  {
            throw new RequiredArgumentException('Unknone field "' . $sField . '"');
        }
    }

    public function __toString()
    {
        return $this->parseVars($this->sTitleSettings);
    }

    public function setDDFieldNames($aDDFieldNames)
    {
        $this->aDDFieldNames = $aDDFieldNames;
        return $this;
    }
}