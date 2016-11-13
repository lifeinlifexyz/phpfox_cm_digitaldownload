<?php

namespace Apps\CM_DigitalDownload\Controller\Admin;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddCategoryController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin();
        /**
         * @var $oCategoryService IFormly
         */
        $oCategoryService = Phpfox::getService('digitaldownload.category');

        if (($iId = $this->request()->getInt('id'))) {
            $oCategoryService->setKey($iId);
        }

        $oForm = $oCategoryService->getForm([
            'action' => $this->url()->makeUrl('current'),
        ]);

        if ($iId) {
            unset($oForm['parent_id']);
            unset($oForm['is_active']);
        }

        if ($_POST && $oForm->isValid()) {
            $oForm->save();
            $this->url()->send('admincp.app',
                [
                    'id' => 'CM_DigitalDownload',
                ],
                _p('Successfully saved the category.'));
        }
        $sTitle = !empty($iId) ? _p('Edit category') : _p('Add category');
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
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_admincp_add_category_clean')) ? eval($sPlugin) : false);
    }
}