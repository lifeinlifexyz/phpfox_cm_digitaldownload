<?php
namespace Apps\CM_DigitalDownload\Block;

class LastUserViewed extends \Phpfox_Component
{
    public function process()
    {
        $sLastViewed = \Phpfox::getCookie('cm_dd_last_viewed');
        $aLastViewed = json_decode($sLastViewed, true);
        if (!empty($aLastViewed)) {

            foreach($aLastViewed as $iKey => $iId) {
                $aLastViewed[$iKey] = (int) $iId;
            }

            $oDD = $this->getParam('oDD');

            $sLastViewed = implode(',', $aLastViewed);

            $aDDs =  \Phpfox::getService('digitaldownload.browse')
                ->conditions([
                    ' AND `d`.`is_active` = 1',
                    ' AND `d`.`id` <> ' . (int) $oDD['id'],
                    ' AND `d`.id IN (' . $sLastViewed . ')',
                ])
                ->limit(\Phpfox::getParam('cm_dd_last_viewed_limit', 10))
                ->page(1)
                ->getCollection();

            if (\Phpfox::getService('digitaldownload.browse')->count() > 0) {
                $this->template()
                    ->assign([
                        'aDDs' => $aDDs,
                    ]);
            }
        }
        return 'block';
    }
}