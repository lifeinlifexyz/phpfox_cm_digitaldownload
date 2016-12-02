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
}