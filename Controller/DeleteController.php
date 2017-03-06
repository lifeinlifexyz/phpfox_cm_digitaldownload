<?php

namespace Apps\CM_DigitalDownload\Controller;


class DeleteController extends AbstractModeration
{
    /**
     * Controller
     */
    public function process()
    {
        $this->checkPerm();
        \Phpfox::getService('digitaldownload.dd')->delete($this->aDD['id']);
        $this->url()->send('digitaldownload', [], _p('Item successfully deleted'));
    }
}