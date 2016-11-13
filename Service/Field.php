<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;

class Field extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $_sTable = 'digital_download_fields';
    protected $sKeyName = 'field_id';

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        $aTypeList = $this->getTypesList();
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
                'rules' => 'required|alphabet',
            ],
            'caption_phrase' => [
                'type' => 'mstring',
                'name' => 'caption_phrase',
                'title' => _p('Caption'),
                'rules' => 'required',
            ],
            'rules' => [
                'type' => 'select',
                'name' => 'rules',
                'title' => _p('Validation rules'),
                'items' => [
                    'required' => _p('Required'),
                    'alphabet' => _p('Alphabet'),
                    'email' => _p('Email'),
                    'min' => _p('Min Value'),
                    'max' => _p('Max Value'),
                    'minLength' => _p('Max Length'),
                    'maxLength' => _p('Max Length'),
                    'length' => _p('Length'),
                    'in' => _p('In'),
                ],
            ],
            'is_active' => [
                'type' => 'boolean',
                'name' => 'is_active',
                'title' => _p('Active'),
                'rules' => '0:1:in',
                'filter' => function ($sValue) {
                    return (int)$sValue;
                }
            ],
        ];
    }

    public function getTypesList()
    {
        return [
            'boolean' => _p('Boolean'),
            'mstring' => _p('Multi language string'),
            'string' => _p('Multi language string'),
            'select' => _p('Multi language string'),
        ];
    }

    public function all()
    {
        return $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->order("`ordering` ASC")
            ->execute('getslaverows');
    }

    /**
     * activae/deactivate category by ids
     * @param $iStatus integer
     * @param $iIds array
     * @return bool
     */
    public function setStatus($iStatus, $iIds)
    {
        $iIds = (array)$iIds;
        return $this->database()->update(\Phpfox::getT($this->_sTable),
            ['`is_active`' => $iStatus], '`category_id` in (' . implode(',', $iIds) . ')');
    }


    /**
     * @param array $aOrders
     * @return $this
     */
    public function order($aOrders = [])
    {
        foreach ($aOrders as $iKey => $aOrder) {
            $aData = [
                'parent_id' => $aOrder['parent_id'],
                'ordering' => $iKey,
            ];
            $this->database()->update(\Phpfox::getT($this->_sTable), $aData, '`category_id` = ' . $aOrder['id']);
        }
        return $this;
    }

    public function delete($iId)
    {
        $this->database()->delete(\Phpfox::getT($this->_sTable), '`field_id` = ' . $iId);
        //todo:: trigger event after category deleted
    }

}