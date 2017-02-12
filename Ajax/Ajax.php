<?php
namespace Apps\CM_DigitalDownload\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function setFieldStatus()
    {
        \Phpfox::isAdmin(true);
        \Phpfox::getService('digitaldownload.field')->setStatus($this->get('id'), $this->get('status'));
    }

    public function deleteImage()
    {
        \Phpfox::isUser(true);
        $iDDid = $this->get('dd_id');
        $iId = $this->get('id');
        $oDD = \Phpfox::getService('digitaldownload.dd')->getDisplayer($iDDid);

        if (Phpfox::getUserId() == $oDD['user_id']) {
            $aImages = $oDD['images'];
            if (isset($aImages[$iId])) {
                $aImage = $aImages[$iId];
                $sFilePath = Phpfox::getParam('core.dir_pic') . 'digitaldownload/' . sprintf($aImage['image_path'], '');
                \Phpfox_File::instance()->unlink($sFilePath);
                unset($aImages[$iId]);
            }
            $aVal = $oDD->getRow();
            $aVal['images'] = json_encode($aImages);
            \Phpfox::getService('digitaldownload.dd')->updateById($iDDid, $aVal);
        } else {
            $this->alert(_p('You are not owner for this item'));
        }
    }


    public function moderation()
    {
        Phpfox::isUser(true);
        $this->template()->getTemplate();
        switch($this->get('action')) {

            case 'deactivate':

                Phpfox::getUserParam('digitaldownload.can_activate_deactivate_other', true);
                $sUrl = \Phpfox_Url::instance()->makeUrl('digitaldownload.activate');

                foreach ((array) $this->get('item_moderate') as $iId) {
                    Phpfox::getService('digitaldownload.dd')->deactivate($iId);
                    $sLink = ' <a href="' . $sUrl . $iId .'" class="js_dd_activate color-success"
                                    title="' . _p('Activate') . '" data-toggle="tooltip">
                                        <i class="fa fa-eye"></i>
                                    </a>';

                    $this->replaceWith('#js_dd_item_holder_' . $iId . ' .js_dd_deactivate', $sLink);
                }
                $sMessage = _p('Items successfully deactivated');

                break;

            case 'activate':

                Phpfox::getUserParam('digitaldownload.can_activate_deactivate_other', true);
                $sUrl = \Phpfox_Url::instance()->makeUrl('digitaldownload.deactivate');

                foreach ((array) $this->get('item_moderate') as $iId) {
                    Phpfox::getService('digitaldownload.dd')->activateByModerator($iId);
                    $sLink = ' <a href="' . $sUrl . $iId .'" class="js_dd_deactivate color-warning"
                                    title="' . _p('Daactivate') . '" data-toggle="tooltip">
                                        <i class="fa fa-eye-slash"></i>
                                    </a>';

                    $this->replaceWith('#js_dd_item_holder_' . $iId . ' .js_dd_activate', $sLink);
                }
                $sMessage =  _p('Items successfully activated');
                break;

            case  'delete':
                Phpfox::getUserParam('digitaldownload.can_delete_other', true);
                foreach ((array) $this->get('item_moderate') as $iId) {
                    Phpfox::getService('digitaldownload.dd')->delete($iId);
                    $this->slideUp('#js_dd_item_holder_' . $iId);
                }
                $sMessage = _p('Items successfully deleted');
                break;

            default:
                $sMessage = _p('Undefined action!');
        }

        $this->alert($sMessage, _p('Moderation'), 300, 150, true);
        $this->hide('.moderation_process');
    }
}