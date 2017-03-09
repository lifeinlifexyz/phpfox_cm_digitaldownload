<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Cache\CMCache;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;

class Field extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $_sTable = 'digital_download_fields';
    protected $sKeyName = 'field_id';
    protected $sAttachTable = 'digital_download';

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        $aTypeList = $this->getTypesList();
        $aAllFields = $this->all();
        $aExitsFields = [];
        foreach($aAllFields as $aField) {
            $aExitsFields[] = $aField['name'];
        }

        return [
            'type' => [
                'type' => 'select',
                'name' => 'type',
                'title' => _p('Type'),
                'items' => $aTypeList,
                'rules' => 'required|' . implode(':', array_keys($aTypeList)) . ':in',
                'errorMessages' => [
                    'type.in' => _p('Undefined type'),
                ],
            ],
            'name' => [
                'type' => 'string',
                'name' => 'name',
                'title' => _p('Name'),
                'rules' => 'required|/^[A-Za-z][A-Za-z0-9_]{1,}$/:regex|' . (!empty($aExitsFields)
                        ? implode(':', $aExitsFields) . ':notin'
                        : 'notin'),
                'errorMessages' => [
                    'name.notin' =>_p('The value entered into \'Name\' field is already used, please choose a different value.'),
                    'name.regex' => _p('The value entered into \'Name\' must be any non-whitespace chars.'),
                ]
            ],
            'caption_phrase' => [
                'type' => 'mstring',
                'name' => 'caption_phrase',
                'title' => _p('Caption'),
                'module' => 'digitaldownload',
                'rules' => 'required',
            ],
            'rules' => [
                'type' => 'select',
                'name' => 'rules',
                'template' => '@CM_DigitalDownload/form/fields/rules-input.html',
                'title' => _p('Validation rules'),
                'items' => [
                    'required' => _p('Required'),
                    'alphabet' => _p('Alphabet'),
                    'num' => _p('Numeric'),
                    'email' => _p('Email'),
                    'url' => _p('Url'),
                    'min' => _p('Min Value'),
                    'max' => _p('Max Value'),
                    'minLength' => _p('Min Length'),
                    'maxLength' => _p('Max Length'),
                ],
            ],
            'is_filter' => [
                'type' => 'boolean',
                'name' => 'is_filter',
                'title' => _p('Is searchable?'),
            ],
            'is_active' => [
                'type' => 'boolean',
                'name' => 'is_active',
                'title' => _p('Active'),
            ],
        ];
    }

    public function getTypesList()
    {
        return [
            'boolean' => _p('Boolean'),
            'mstring' => _p('Multi language string'),
            'string' => _p('String'),
            'text' => _p('Text'),
            'dd' => _p('Digital Download'),
        ];
    }

    /**
     * @param Form $oForm
     * @return $this
     */
    public function addField(Form $oForm)
    {
        $sType = $oForm->getFieldValue('type');
        $sName = $oForm->getFieldValue('name');
        $oType = $oForm->getFieldFactory()->createType($sType, ['name' => '', 'title' => '']);
        $aColumnsDefs = $oType->getColumnDefinitions();
        $sTable = \Phpfox::getT($this->sAttachTable);
        foreach($aColumnsDefs as &$aColumnsDef) {
            $aColumnsDef['table'] = $sTable;
            $aColumnsDef['field'] = isset($aColumnsDef['field'])
                ? $sName . '_' .$aColumnsDef['field']
                : $sName;
            if (!$this->database()->isField($sTable, $aColumnsDef['field'])) {
                $this->database()->addField($aColumnsDef);
            }
        }
        CMCache::removeByGroup('cm_dd_category_fields');
        return $this;
    }

    public function all()
    {
        return $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->order("`ordering` ASC")
            ->execute('getslaverows');
    }

    public function setStatus($iId, $iStatus)
    {
        $iId = (int) $iId;
        CMCache::removeByGroup('cm_dd_category_fields');
        return $this->database()->update(\Phpfox::getT($this->_sTable),
            ['`is_active`' => $iStatus], '`field_id` = ' . $iId);
    }

    public function delete($iId)
    {
        try {
            $sFieldName = $this->database()
                ->select('name')
                ->from(\Phpfox::getT($this->_sTable))
                ->where('`field_id` = ' . $iId)
                ->execute('getslavefield');
            if (!$sFieldName) {
                return false;
            }
            $this->database()->beginTransaction();
            $this->database()->delete(\Phpfox::getT($this->_sTable), '`field_id` = ' . $iId);
            $this->database()->dropField(\Phpfox::getT($this->sAttachTable), $sFieldName);
            $this->database()->commit();

            CMCache::removeByGroup('cm_dd_category_fields');

            return true;
        } catch (\Exception $e)
        {
            $this->database()->rollback();
            throw $e;
        }

    }

    public function getFilterable(array $aCategoryIds = [])
    {
        $aCond  = [
            '`f`.`is_active` = 1',
            'AND `c`.`is_active` = 1',
            'AND `f`.`is_filter` = 1',
        ];

        if (count($aCategoryIds) > 0) {
            $aCond[] = 'AND `c`.`category_id` in (' . implode(', ', $aCategoryIds) . ')';
        }
        $sCacheKey = 'cm_dd_filterable_fields_' . implode('_', $aCategoryIds);
        $that = $this;
        return CMCache::remember($sCacheKey, function() use ($that, $aCond){
            return $this->database()
                ->select('f.*')
                ->from(\Phpfox::getT($this->_sTable), 'f')
                ->leftJoin(\Phpfox::getT('digital_download_category_fields'), 'cf', 'f.field_id = cf.field_id')
                ->leftJoin(\Phpfox::getT('digital_download_category'), 'c', 'c.category_id = cf.category_id')
                ->group('`name`')
                ->where($aCond)
                ->order('`f`.`ordering` ASC')
                ->execute('getslaverows');
        }, 0, 'cm_dd_category_fields');
    }

    public function getFilterableFieldsName()
    {
        $aFields = $this->getFilterable();
        $aList = [];
        foreach($aFields as $aField) {
            $aList[$aField['name']] = $aField['name'];
        }
        return $aList;
    }

    public function getFieldsByType($sType, $bActive = true)
    {
        $that = $this;
        return CMCache::remember('cm_dd_field_types_' . $sType . (string) $bActive,
            function() use ($that, $sType, $bActive) {
                $aCond = [
                    ' AND `type` = \'' . $sType . '\'',
                 ];

                if ($bActive) {
                    $aCond[] = ' AND `is_active` = ' . (int) $bActive;
                }

                $aFields = $this->database()
                    ->select('`name`')
                    ->from(\Phpfox::getT($this->_sTable))
                    ->where($aCond)
                    ->all();

                $aRes =  [];
                foreach($aFields as $aField) {
                    $aRes[] = $aField['name'];
                }
                return $aRes;
        }, 0, 'cm_dd_category_fields');
    }

}