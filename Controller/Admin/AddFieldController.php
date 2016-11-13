<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddFieldController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        /**
         * @var $oFieldService IFormly
         */
        $oFieldService = Phpfox::getService('digitaldownload.field');

        if (($iId = $this->request()->getInt('id'))) {
            $oFieldService->setKey($iId);
        }

        $oForm = $oFieldService->getForm([
            'action' => $this->url()->makeUrl('digitaldownload.admincp.save-field'),
            'form_id' => 'digitaldownload-field',
        ]);

        $sTitle = !empty($iId) ? _p('Edit field') : _p('Add field');
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
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_add_field_clean')) ? eval($sPlugin) : false);
    }
}