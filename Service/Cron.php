<?php
namespace Apps\CM_DigitalDownload\Service;


use Phpfox;
use Phpfox_Plugin;

class Cron  extends \Phpfox_Service
{
    protected $_sTable = 'digital_download';

    public function expireDD()
    {
        //get expire dd
        $aExpireDDs = Phpfox::getService('digitaldownload.browse')->conditions(
            [
                '`d`.`expire_timestamp` <= ' . time(),
                ' AND `d`.`is_expired` =  0'
            ])->limit(100)
            ->sort('`d`.`id` ASC')
            ->page(1)
            ->getCollection();

        $aVal = [
            'is_expired' => 1,
            'is_active' => 0,
        ];

        $aPlanOption = \Phpfox::getService('digitaldownload.plan')->getExtraOptions();
        //inactivate all plan options for dd
        foreach($aPlanOption as &$sPlanOption) {
            $aVal[$sPlanOption] = 0;
        }


        $aUserDDs = [];
        $aExpireIDDs = [];
        foreach($aExpireDDs as $oDD) {
            $aExpireIDDs[] = $oDD['id'];
            $aUserDDs[$oDD['user_id']]['dd_list'] .= '<li><a href="' . $oDD['url'] .'">' . ((string) $oDD). '</a></li>';
            $aUserDDs[$oDD['user_id']]['full_name'] = $oDD['full_name'];
        }

        (($sPlugin = Phpfox_Plugin::get('digitaldownload.service_before_expire_dd')) ? eval($sPlugin) : false);

        //expire
        $this->database()->update(\Phpfox::getT($this->_sTable), $aVal,
            '`id` IN (' . implode(',', $aExpireIDDs) . ')');
        //delete assigned plan
        $this->database()->delete(Phpfox::getT('digital_download_dd_plan'),
            'dd_id IN (' . implode(',', $aExpireIDDs) . ')');

        //send email to owners
        foreach($aUserDDs as $iUserId => &$aUserDD) {
            Phpfox::getLib('mail')->to($iUserId)
                ->subject(['digitaldownload_item_expired_subject', ['web_site' => Phpfox::getBaseUrl()]])
                ->message(['digitaldownload_item_expired_message', [
                    'full_name' => $aUserDD['full_name'],
                    'web_site' =>  Phpfox::getBaseUrl(),
                    'item_list' => $aUserDD['dd_list']]])
                ->send();
        }
    }
}