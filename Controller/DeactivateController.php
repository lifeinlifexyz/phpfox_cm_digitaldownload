<?php

namespace Apps\CM_DigitalDownload\Controller;


class DeactivateController extends AbstractModeration
{
    /**
     * Controller
     */
    public function process()
    {
        $this->checkPerm();
        \Phpfox::getService('digitaldownload.dd')->deactivate($this->aDD['id']);
        $this->url()->send('digitaldownload.my', [], _p('Item successfully deactivated'));
    }
}