<?php

namespace Apps\CM_DigitalDownload\Controller;


class ActivateController extends AbstractModeration
{
    /**
     * Controller
     */
    public function process()
    {
        $this->checkPerm();
        if ($this->aDD['is_expired']) {
            $this->url()->send('digitaldownload.add.options');
        }
        if (\Phpfox::getUserParam('digitaldownload.can_activate_deactivate_other')) {
            \Phpfox::getService('digitaldownload.dd')->activateByModerator($this->aDD['id']);
        } else {
            \Phpfox::getService('digitaldownload.dd')->activate($this->aDD['id']);
        }
        $this->url()->send('digitaldownload.my', [], _p('Items successfully activated'));
    }
}