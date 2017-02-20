<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
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
        user('digitaldownload.cm_dd_add', null, null, true);
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_add_digitaldownload')) ? eval($sPlugin) : false);

        $sAction = $this->request()->get('req4');
        $oDigitalDownload = \Phpfox::getService('digitaldownload.dd');
        if (($bEdit = $this->request()->get('dd_id'))) {

            $aDD = $oDigitalDownload->getForEdit((int)$bEdit);

            //activation after expired
            if (empty($aDD['plan_info']) && ($iPlan = $this->request()->getInt('plan_id'))) {
                Phpfox::getService('digitaldownload.plan')->assign($bEdit, $iPlan);
                $aDD = $oDigitalDownload->getForEdit((int)$bEdit);
            }

            if (empty($aDD)) {
                return Phpfox_Error::display(_p('Unable to find the item you are editing for'));
            }

            if ((!Phpfox::isAdmin()) && ($aDD['user_id'] != Phpfox::getUserId())) {
                return Phpfox::getLib('module')->setController('error.404');
            }

            $oDigitalDownload->setKey((int)$bEdit);
            $oDD = $oDigitalDownload
                ->setRow($aDD)
                ->getDisplayer($bEdit);

            $aPlan = json_decode($oDD['plan_info'], true);

            if (!$oDD['is_active'] && $aPlan['price'] == '0.00') {

                (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_activate_digitaldownload')) ? eval($sPlugin) : false);
                $oDigitalDownload->activate($oDD['id'], $aPlan);
                (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_activate_digitaldownload')) ? eval($sPlugin) : false);
            }

            $this->setParam('oDD', $oDD);
            $this->setParam('aPlan', $aPlan);
            unset($oDD['plan_info']);

            $aOptions = $this->request()->getArray('options');

            if (($this->request()->get('options_apply') && count($aOptions) > 0) || $this->request()->get('confirm_pay')) {
                $this->setParam('aOptions', $aOptions);
                return Phpfox::getLib('module')->setController('digitaldownload.apply-options');
            }

        }

        if (!$bEdit) {
            $iCategory = $this->request()->getInt('category_id');

            if (!$iCategory) {
                $this->setParam('url', $this->url()->makeUrl('current'));
                return Phpfox::getLib('module')->setController('digitaldownload.category');
            }


            $oDigitalDownload->setCategoryId($iCategory);

            $iPlan = $this->request()->getInt('plan_id');

            if (!$iPlan) {
                $this->setParam('url', $this->url()->makeUrl('current', ['category_id' => $iCategory]));
                return Phpfox::getLib('module')->setController('digitaldownload.plan');
            }

        }

        $oForm = $oDigitalDownload->getForm([
            'enctype' => 'multipart/form-data'
        ]);

        if ($sAction == 'upload') {
            $this->upload($oDD);
        }

        if ($_POST && $oForm->isValid()) {
            $sPlugin = $bEdit
                ? 'digitaldownload.before_update_digitaldownload'
                : 'digitaldownload.before_create_digitaldownload';

            (($sPlugin = Phpfox_Plugin::get($sPlugin)) ? eval($sPlugin) : false);

            $oForm->addField('hidden', [
                'name' => 'user_id',
                'value' => Phpfox::getUserId(),
            ]);
            
            if (!$bEdit) {
                $oForm->addField('hidden', [
                    'name' => 'time_stamp',
                    'value' => PHPFOX_TIME,
                ]);
            }

            db()->beginTransaction();
            $iId = $oForm->save();

            if (!$bEdit && isset($iPlan)) { //if add, assign plan to dd
                Phpfox::getService('digitaldownload.plan')->assign($iId, $iPlan);
            }

            if ($bEdit && $this->request()->get('do_invite')) {
                $aFriends = $this->request()->getArray('friend');
                $aInvite = [
                    'emails' => $this->request()->get('invite_emails'),
                    'invite_from' => $this->request()->get('invite_invite_from'),
                    'personal_message' => $this->request()->get('invite_personal_message'),
                ];

                $aInvite['invite'] = !empty($aFriends) ? $aFriends : null;

                if (!empty($aInvite)) {
                    Phpfox::getService('digitaldownload.invite')->send($iId, $aInvite, $oDD);
                }
            }

            $sPlugin = $bEdit
                ? 'digitaldownload.after_update_digitaldownload'
                : 'digitaldownload.after_create_digitaldownload';
            (($sPlugin = Phpfox_Plugin::get($sPlugin)) ? eval($sPlugin) : false);
            db()->commit();

            $this->url()->send('digitaldownload.add',['dd_id' => $iId], _p('Digital Download successfully saved'));
        }
        $sTitle = $bEdit ? _p('Editing') : _p('Creating');
        $sUrl = $bEdit ? $this->url()->makeUrl('digitaldownload.add.' . $bEdit) : $this->url()->makeUrl('digitaldownload.add');

        if ($bEdit)
        {
            $aMenus = array(
                'detail' => _p('Details'),
                'photo' => _p('Photo'),
                'invite' => _p('Invite'),
                'options' => _p('Options'),
            );


            $this->template()->buildPageMenu('js_mp_block',
                $aMenus,
                [
                    'link' => $this->url()->permalink('digitaldownload', $bEdit),
                    'phrase' => _p('View')
                ]
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
                    'sMyEmail' => Phpfox::getUserBy('email'),
                ]
            );
    }

    private function upload($oDD)
    {
        $aPhotos = $oDD['images'];
        $aPlan = $this->getParam('aPlan');
        $iPhotoMax = $aPlan['allowed_count_pictures'];

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