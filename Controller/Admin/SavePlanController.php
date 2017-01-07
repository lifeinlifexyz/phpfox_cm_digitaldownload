<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class SavePlanController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        /**
         * @var $oPlanService IFormly
         */
        $oPlanService = Phpfox::getService('digitaldownload.plan');

        if (($iId = $this->request()->getInt('field_id'))) {
            $oPlanService->setKey($iId);
        }

        $oForm = $oPlanService->getForm([
            'action' => $this->url()->makeUrl('current'),
            'form_id' => 'dd-plan',
        ]);

        if ($iId) {
            unset($oForm['name']);
            unset($oForm['type']);
        }

        if ($oForm->isValid()) {
            try {
                db()->beginTransaction();
                $oForm->save();
                db()->commit();
            } catch (\Exception $e) {
                db()->rollback();
                throw  $e;
            }

            $sMessage = _p('Successfully saved plan.');
            $this->url()->send('admincp.app', ['id' => 'CM_DigitalDownload'], $sMessage);
        } else {
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
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_save_plan_clean')) ? eval($sPlugin) : false);
    }
}