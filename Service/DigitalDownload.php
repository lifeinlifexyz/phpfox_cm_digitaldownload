<?php
namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;

class DigitalDownload  extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $_sTable = 'digital_download';
    protected $sKeyName = 'id';
    protected $iCategoryId = null;

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        if (!$this->iCategoryId) {
            throw new \InvalidArgumentException('Category id is null');
        }
        //todo:: save to cache category fields
        $aRawFields =\Phpfox::getService('digitaldownload.categoryField')->getInfoByCategoryId($this->iCategoryId);
        $aFields = [];
        foreach($aRawFields as &$aRawField) {
            $aFields[$aRawField['name']] = $this->buildFieldInfo($aRawField);
        }

        //add system fields
        $aFields['category_id'] = [
            'type' => 'hidden',
            'name' => 'category_id',
            'title' => '',
            'value' => $this->iCategoryId,
        ];

        return $aFields;
    }

    protected function buildFieldInfo($aRawField)
    {
        //todo:: build field info array certain field type. it is simple type with out extra data
        $aRes =  [
            'type' => $aRawField['type'],
            'name' => $aRawField['name'],
            'title' => _p($aRawField['caption_phrase']),
            'rules' => !empty($aRawField['rules']) ?  $aRawField['rules']: null,
        ];

        return $aRes;
    }

    /**
     * @param $iCategoryId
     * @return $this
     */
    public function setCategoryId($iCategoryId)
    {
        $this->iCategoryId = $iCategoryId;
        return $this;
    }

}