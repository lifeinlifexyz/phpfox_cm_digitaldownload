<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class PlanController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        Phpfox::isUser(true);
        $sUrl = $this->getParam('url');

        if (is_null($sUrl)) {
            $sUrl = \Phpfox_Url::instance()->makeUrl('digitaldownload.add.options', [
                'dd_id' => $this->request()->getInt('dd_id'),
            ]);
        }

        $aPlans = $this->getParam('aPlans') ? $this->getParam('aPlans')
            : Phpfox::getService('digitaldownload.plan')->collection();

        $this->template()->setTitle(_p('Select Plan'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Choose plan'))
            ->assign([
                    'aPlans' => $aPlans,
                    'sUrl' => $sUrl,
                ]
            );

        if ($this->request()->get('reg2') == 'choose') {
            $this->template()->setTemplate('test');
        }
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