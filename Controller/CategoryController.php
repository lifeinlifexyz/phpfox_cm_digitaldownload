<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class CategoryController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        Phpfox::isUser(true);
        user('cm_dd_add', null, null, true);

        $this->template()->setTitle(_p('Select Category'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Add digital download'), $this->url()->makeUrl('digitaldownload.add'))
            ->assign([
                    'aCategory' => Phpfox::getService('digitaldownload.category')->getActive(),
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