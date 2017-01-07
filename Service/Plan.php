<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;

class Plan extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $_sTable = 'digital_download_plans';
    protected $sKeyName = 'plan_id';

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        return [
            'name' => [
                'type' => 'mstring',
                'name' => 'name',
                'title' => _p('Name'),
                'rules' => 'required',
            ],
            'allowed_count_pictures' => [
                'type' => 'string',
                'name' => 'allowed_count_pictures',
                'title' => _p('Allowed pictures count'),
                'rules' => 'required|num|1:min',
                'value' => 1,
            ],
            'life_time' => [
                'type' => 'string',
                'name' => 'life_time',
                'title' => _p('Life time(in day)'),
                'rules' => 'required|num|1:min',
                'value' => 1,
            ],
        ];
    }

    public function all()
    {
        return $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->order("`plan_id` DESC")
            ->execute('getslaverows');
    }

    public function setStatus($iId, $iStatus)
    {
        $iId = (int) $iId;
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
            'AND `f`.`is_filter` = 1'
        ];

        if (count($aCategoryIds) > 0) {
            $aCond[] = 'AND `c`.`category_id` in (' . implode(', ', $aCategoryIds) . ')';
        }

        //todo::cache
        return $this->database()
            ->select('f.*')
            ->from(\Phpfox::getT($this->_sTable), 'f')
            ->leftJoin(\Phpfox::getT('digital_download_category_fields'), 'cf', 'f.field_id = cf.field_id')
            ->leftJoin(\Phpfox::getT('digital_download_category'), 'c', 'c.category_id = cf.category_id')
            ->where($aCond)
            ->order('`f`.`ordering` ASC')
            ->execute('getslaverows');
    }

    public function getFilterableFieldsName()
    {
        //todo::cache
        $aFields = $this->getFilterable();
        $aList = [];
        foreach($aFields as $aField) {
            $aList[$aField['name']] = $aField['name'];
        }
        $aList['price'] = 'price';
        return $aList;
    }

}