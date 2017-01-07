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
        //todo extend options
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

    public function delete($iId)
    {
        $this->database()->delete(\Phpfox::getT($this->_sTable), '`plan_id` = ' . $iId);
        return $this;
    }


}