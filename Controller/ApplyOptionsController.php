<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ApplyOptionsController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        Phpfox::isUser(true);
        $aOptions = $this->getParam('aOptions');
        $oDD = $this->getParam('oDD');

        $aFreeOptions = isset($aOptions['free']) ? $aOptions['free'] : [];
        $aPaidOptions = isset($aOptions['paid']) ? $aOptions['free'] : [];
        $extraOptions = Phpfox::getService('digitaldownload.plan')->getExtraOptions();


        if (count($aFreeOptions) > 0) {
            //apply free options
            $aVal = [];
            foreach($aFreeOptions as $sOptionName => $iOption) {
                if (in_array($sOptionName, $extraOptions)) {
                    $aVal[$sOptionName] = (int) $iOption;
                }
            }
            Phpfox::getService('digitaldownload.dd')->updateById($oDD['id'], $aVal);
        }

        if (!(count($aPaidOptions) > 0) && (isset($aVal) && count($aVal) > 0)) {
            $this->url()->send('digitaldownload.add',['dd_id' => $oDD['id']], _p('Free options successfully applied'));
        }


        $this->template()->setTitle(_p('Select Plan'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Add digital download'), $this->url()->makeUrl('digitaldownload.add'))
            ->assign([
                    'aPlans' => Phpfox::getService('digitaldownload.plan')->collection(),
                    'sUrl' => $this->getParam('url'),
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_select_category_clean')) ? eval($sPlugin) : false);
    }
}