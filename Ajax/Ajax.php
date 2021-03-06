<?php
namespace Apps\CM_DigitalDownload\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function setFieldStatus()
    {
        \Phpfox::isAdmin(true);
        \Phpfox::getService('digitaldownload.field')->setStatus($this->get('id'), $this->get('active'));
    }

    public function deleteImage()
    {
        \Phpfox::isUser(true);
        $iDDid = $this->get('dd_id');
        $iId = $this->get('id');
        $oDD = \Phpfox::getService('digitaldownload.dd')->getDisplayer($iDDid);

        if ((Phpfox::getUserId() == $oDD['user_id']) || Phpfox::getUserParam('digitaldownload.can_edit_other')) {
            $aImages = $oDD['images'];
            if (isset($aImages[$iId])) {
                $aImage = $aImages[$iId];
                $sFilePath = Phpfox::getParam('core.dir_pic') . 'digitaldownload/' . sprintf($aImage['image_path'], '');
                \Phpfox_File::instance()->unlink($sFilePath);
                unset($aImages[$iId]);
            }
            $aVal = [];
            $aVal['images'] = json_encode($aImages);
            \Phpfox::getService('digitaldownload.dd')->updateById($iDDid, $aVal);
        } else {
            $this->alert(_p('You are not owner for this item'));
        }
    }

    public function rate()
    {
        Phpfox::getUserParam('digitaldownload.can_rate', true);
        $iId = (int)$this->get('id');
        $aRow = \Phpfox::getService('digitaldownload.dd')->getForEdit($iId);

        if ($aRow['user_id'] == Phpfox::getUserId()) {
            $this->alert('You can not set rating for own item');
            return false;
        }

        $iRate = (int) $this->get('rate');
        Phpfox::getService('digitaldownload.rating')->setRating($iId, $iRate, Phpfox::getUserId());
        $aRow = \Phpfox::getService('digitaldownload.dd')->getForEdit($iId);
        $oDD = \Phpfox::getService('digitaldownload.dd')->setRow($aRow)->getDisplayer($iId);
        $sRating = $oDD['rating'];
        $this->replaceWith('.cm-dd-rating-' . $iId, $sRating);
        $this->call('initCMAjaxLink();');
        $this->alert(_p('Successfully rated'));
        return $this->bIsModeration;
    }


    public function moderation()
    {
        Phpfox::isUser(true);
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

    public function deleteInvoice(){
        Phpfox::isUser(true);
        $iInvoice = $this->get('invoice');
        if (!empty($iInvoice)){
            if (Phpfox::getService('digitaldownload.invoice')->delete($iInvoice)){
                $this->hide('#data-invoice-id-'.$iInvoice);
            }else{
                $this->alert(_p('Cannot find this invoice'));
            }
        }
    }
}