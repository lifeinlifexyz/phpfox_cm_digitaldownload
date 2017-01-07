<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddPlanController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        /**
         * @var $oPlanService IFormly
         */
        $oPlanService = Phpfox::getService('digitaldownload.plan');

        if (($iId = $this->request()->getInt('id'))) {
            $oPlanService->setKey($iId);
        }

        $oForm = $oPlanService->getForm([
            'action' => $this->url()->makeUrl('digitaldownload.admincp.save-plan'),
            'form_id' => 'dd-plan',
        ]);

        if ($iId) {

            $oForm->addField('hidden', [
                'name' => 'field_id',
                'value' => $iId,
            ]);

        }

        $sTitle = !empty($iId) ? _p('Edit plan') : _p('Add plan');
        $this->template()
            ->setTitle($sTitle)
            ->setBreadCrumb($sTitle)
            ->assign('form', $oForm);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_add_plan_clean')) ? eval($sPlugin) : false);
    }
}