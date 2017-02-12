<?php
namespace Apps\CM_DigitalDownload\Controller;


abstract class AbstractModeration extends \Phpfox_Component
{
    protected $aDD = [];

    protected function checkPerm()
    {
        \Phpfox::isUser();
        $iId = $this->request()->getInt('req3');
        if (empty($iId)) {
            \Phpfox_Error::display(_p('Id is not set'));
            return false;
        }

        $this->aDD = \Phpfox::getService('digitaldownload.dd')->getForEdit($iId);

        if ($this->aDD['user_id'] != \Phpfox::getUserId()/* || (!\Phpfox::getUserParam('digitaldownload.can_activate_deactivate_other'))*/) {
            \Phpfox_Error::display(_p('You have not permission for activate this item'));
            return false;
        }

        return true;
    }
}