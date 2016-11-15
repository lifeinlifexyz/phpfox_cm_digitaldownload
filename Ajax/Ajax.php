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
}