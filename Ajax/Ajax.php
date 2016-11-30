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
            \Phpfox::getService('digitaldownload.images')->delete($iId);
        } else {
            $this->alert(_p('You are not owner for this item'));
        }
    }
}