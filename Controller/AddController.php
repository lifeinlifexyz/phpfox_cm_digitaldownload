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
        $oForm = $oDigitalDownload->getForm([
            'enctype' => 'multipart/form-data'
        ]);

        if ($_POST && $oForm->isValid()) {
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_add_digitaldownload')) ? eval($sPlugin) : false);
            $oForm->save();
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_add_digitaldownload')) ? eval($sPlugin) : false);
        }

        $this->template()->setTitle(_p('Creating a Digital Download'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Add Digital Download'), $this->url()->makeUrl('digitaldownload.add'))
            ->assign([
                    'oForm' => $oForm,
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