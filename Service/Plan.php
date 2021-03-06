<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Collection\Collection;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Display as BaseDisplay;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;

class Plan extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $_sTable = 'digital_download_plans';
    const DD_PLAN_TABLE = 'digital_download_dd_plan';
    protected $sKeyName = 'plan_id';

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        $aFields = [
            'name' => [
                'type' => 'mstring',
                'name' => 'name',
                'title' => _p('Name'),
                'rules' => 'required',
            ],
            'price' => [
                'type' => 'price',
                'name' => 'price',
                'title' => _p('Digital Download Activation'),
                'rules' => 'required',
                'value' => '0.00',
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

        (($sPlugin = \Phpfox_Plugin::get('digitaldownload.collect_plan_fields')) ? eval($sPlugin) : false);
        $aUserGroups = \Phpfox::getService('user.group')->get();
        $aGroupItems = [];
        foreach($aUserGroups as $aUserGroup) {
            $aGroupItems[$aUserGroup['user_group_id']] = $aUserGroup['title'];
        }
        $aFields['user_groups'] = [
            'type' => 'multilist',
            'name' => 'user_groups',
            'title' => _p('Allowed user groups'),
            'items' => $aGroupItems,
        ];

        return $aFields;
    }

    public function all()
    {
        return $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->order("`plan_id` DESC")
            ->execute('getslaverows');
    }

    public function forCurUser()
    {
        $iGroupId = \Phpfox::getUserBy('user_group_id');
        $this->database()->where('`user_groups` LIKE \'%' . ((int) $iGroupId) .'%\'');
        return $this->all();
    }

    public function collection($bForUser = true)
    {
        $aRows = $bForUser ? $this->forCurUser() : $this->all();
        $oDisplay = new BaseDisplay($this->getForm([
            'form_id' => 'dd-plan',
        ]));
        $oDisplay->setToStrCallback([$this, 'displayExtraOptions']);
        $oCollection = new Collection($aRows, $oDisplay);
        return $oCollection;
    }

    public function displayExtraOptions($oDisplay)
    {
        $sOutput = '';
        try {
            $aExtraOptions = $this->getExtraOptions();
            foreach($aExtraOptions as $sOptionName) {
                if ($oDisplay[$sOptionName . '_allowed']) {
                    $sOutput .= $oDisplay[$sOptionName];
                }
            }
        } catch (\Exception $e) {
            if (PHPFOX_DEBUG) {
                $sOutput .= $e->getMessage();
            }
        }

        return $sOutput;
    }

    public function assign($iDDId, $iPlanId){
        $this->database()->delete(\Phpfox::getT(self::DD_PLAN_TABLE), '`dd_id` = ' . $iDDId);
        $aPlan = $this->get($iPlanId);
        $aVals = [
            'plan_id' => $iPlanId,
            'dd_id' => $iDDId,
            'info' => json_encode($aPlan),
        ];
        return $this->database()->insert(\Phpfox::getT(self::DD_PLAN_TABLE), $aVals);
    }

    public function get($iPlanId) {
        return $this->database()
            ->select('*')
            ->from(\Phpfox::getT($this->_sTable))
            ->where('`plan_id` = ' . $iPlanId)
            ->get();
    }

    public function delete($iId)
    {
        $sPlanName = $this->database()
            ->select('name')
            ->from(\Phpfox::getT($this->_sTable))
            ->where('`plan_id` = ' . $iId)
            ->execute('getSlaveField');
        if ($sPlanName) {
            \Language_Service_Phrase_Process::instance()->delete($sPlanName, true);
        }
        $this->database()->delete(\Phpfox::getT($this->_sTable), '`plan_id` = ' . $iId);
        return $this;
    }

    public function getExtraOptions()
    {
        $aOptions = [];
        (($sPlugin = \Phpfox_Plugin::get('digitaldownload.get_plan_extra_options')) ? eval($sPlugin) : false);
        return $aOptions;
    }


}