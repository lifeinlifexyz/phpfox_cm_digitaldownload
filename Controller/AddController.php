<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;
use Phpfox_Request;

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

        $sAction = $this->request()->get('req4');
        $oDigitalDownload = \Phpfox::getService('digitaldownload.dd');

        if (($bEdit = $this->request()->get('req3'))) {
            $oDigitalDownload->setKey((int)$bEdit);
            $oDD = $oDigitalDownload->getDisplayer($bEdit);
            $this->setParam('oDD', $oDD);
            if (!Phpfox::isAdmin() && $oDD['user_id'] != Phpfox::getMessage()) {
                return Phpfox::getLib('module')->setController('error.404');
            }
        }

        if (!$bEdit) {
            $iCategory = $this->request()->getInt('category_id');

            if (!$iCategory) {
                $this->setParam('url', $this->url()->makeUrl('current'));
                return Phpfox::getLib('module')->setController('digitaldownload.category');
            }
            $oDigitalDownload->setCategoryId($iCategory);

        }

        $oForm = $oDigitalDownload->getForm([
            'enctype' => 'multipart/form-data'
        ]);
        if ($sAction == 'upload') {
            $this->upload($oDD);
        }

        if ($_POST && $oForm->isValid()) {
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_add_digitaldownload')) ? eval($sPlugin) : false);

            $oForm->addField('hidden', [
                'name' => 'user_id',
                'value' => Phpfox::getUserId(),
            ]);

            $oForm->addField('hidden', [
                'name' => 'time_stamp',
                'value' => time(),
            ]);

            $iId = $oForm->save();
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_add_digitaldownload')) ? eval($sPlugin) : false);
        }
        $sTitle = $bEdit ? _p('Editing') : _p('Creating');
        $sUrl = $bEdit ? $this->url()->makeUrl('digitaldownload.add.' . $bEdit) : $this->url()->makeUrl('digitaldownload.add');

        if ($bEdit)
        {
            $aMenus = array(
                'detail' => _p('Details'),
                'photo' => _p('Photo'),
            );


            $this->template()->buildPageMenu('js_mp_block',
                $aMenus,
                array(
                    'link' => $this->url()->permalink('digitaldownload', $bEdit),
                    'phrase' => _p('View')
                )
            );
        }

        $this->template()
            ->setTitle($sTitle)
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb($sTitle, $sUrl, true)
            ->setHeader([
                'progress.js' => 'static_script',
            ])
            ->assign([
                    'sFieldsHtml' => $oForm->render('@CM_DigitalDownload/form/only_fields.html'),
                    'bEdit' => $bEdit,
                ]
            );
    }

    private function upload($oDD)
    {
        $aPhotos = $oDD['images'];
        $iPhotoMax = 3; //todo:: get from plan

        if (count($aPhotos) >= $iPhotoMax) {
            $aRes = [
                'error' => true,
                'messages' => [_p('You have reached images limit')],
            ];
        }

        $oImgService = Phpfox::getService('digitaldownload.images');
        if (!isset($aRes)) {
            if (($mFile = $oImgService->upload($oDD))) {
                list($iImageId, $sFile) = $mFile;
                $aRes = [
                    'error' => false,
                    'image_url' => Phpfox::getLib('phpfox.image.helper')->display([
                        'path' => 'core.url_pic',
                        'file' => 'digitaldownload/' . $sFile,
                        'server_id' => $this->request()->getServer('PHPFOX_SERVER_ID'),
                        'suffix' => '_200',
                        'max_width' => '200',
                        'return_url' => true,
                    ]),
                    'id' => $iImageId,
                ];
            } else {
                $aRes = [
                    'error' => true,
                    'messages' => \Phpfox_Error::get(),
                ];
            }
        }
        header('Content-type: application/json');
        echo json_encode($aRes);
        exit();
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