<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoryFieldsController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin();
        /**
         * @var $oCategoryService IFormly
         */
        $oCategoryFieldService = Phpfox::getService('digitaldownload.categoryField');
        $oFieldService = Phpfox::getService('digitaldownload.field');

        $iId = $this->request()->getInt('id');

        if (($aCatFields = $this->request()->get('catFields', []))) {
            $oCategoryFieldService->sync($aCatFields, $iId);
            $this->url()->send('admincp.app',
                ['id' => 'CM_DigitalDownload'],
                _p('Fields successfully attached to category')
            );
        }
        $aFields = $oCategoryFieldService->getByCategoryId($iId);
        $aAttachedFields = [];
        foreach ($aFields as $aField) {
            $aAttachedFields[] = $aField['field_id'];
        }
        $this->template()
            ->setTitle(_p('Category fields'))
            ->setBreadCrumb(_p('Category fields'))
            ->assign([
                'iId' => $iId,
                'aFields' => $oFieldService->all(),
                'aAttachedFields' => $aAttachedFields,
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_add_category_clean')) ? eval($sPlugin) : false);
    }
}