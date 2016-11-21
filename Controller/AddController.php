<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_add_digitaldownload')) ? eval($sPlugin) : false);

        user('cm_dd_add', null, null, true);
        $iCategory = $this->request()->getInt('category_id');

        if (!$iCategory) {
            $this->setParam('url', $this->url()->makeUrl('current'));
            return Phpfox::getLib('module')->setController('digitaldownload.category');
        }

        $oDigitalDownload = \Phpfox::getService('digitaldownload.dd');
        $oDigitalDownload->setCategoryId($iCategory);
        $oForm = $oDigitalDownload->getForm();

        if ($_POST && $oForm->isValid()) {
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_save_digitaldownload')) ? eval($sPlugin) : false);
            $oForm->save();
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_add_digitaldownload')) ? eval($sPlugin) : false);

        }



        $bIsEdit = false;
        $bIsNewPage = false;
        $sStep = $this->request()->get('req3');
        $aPage = [];

        $this->template()->setTitle(_p('Creating a Digital Download'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Add Digital Download'), $this->url()->makeUrl('digitaldownload.add'))
//            ->setPhrase([
//                    'core.select_a_file_to_upload',
//                ]
//            )
//            ->setHeader([
//                    'privacy.css' => 'module_user',
//                    'progress.js' => 'static_script',
//                ]
//            )
//            ->setHeader(['<script type="text/javascript">$Behavior.groupsProgressBarSettings = function(){ if ($Core.exists(\'#js_groups_block_customize_holder\')) { oProgressBar = {holder: \'#js_groups_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 1, total: 1, frame_id: \'js_upload_frame\', file_id: \'image\'}; $Core.progressBarInit(); } }</script>'])
            ->assign([
                    'oForm' => $oForm,
//                    'aTypes'       => Phpfox::getService('groups.type')->get(),
//                    'bIsEdit'      => $bIsEdit,
//                    'iMaxFileSize' => user('pf_group_max_upload_size', 500) ? Phpfox::getLib('phpfox.file')->filesize((user('pf_group_max_upload_size', 500) / 1024) * 1048576) : null,
//                    'aWidgetEdits' => Phpfox::getService('groups')->getWidgetsForEdit(),
//                    'bIsNewPage'   => $bIsNewPage,
//                    'sStep'        => $sStep,
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}