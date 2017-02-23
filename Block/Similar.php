<?php


namespace Apps\CM_DigitalDownload\Block;


class Similar extends \Phpfox_Component
{
    public function process()
    {
        $oDD = $this->getParam('oDD');
        if ($oDD) {
            $sTitle = (string) $oDD;
            $this->template()
                ->assign([
                    'aDDs' =>  \Phpfox::getService('digitaldownload.browse')
                        ->conditions([
                            ' AND `d`.`is_active` = 1',
                            ' AND `d`.`id` <> ' . (int) $oDD['id'],
                        ])
                        ->similar($sTitle)
                        ->limit(\Phpfox::getParam('cm_dd_similar_limit', 10))
                        ->page(1)
                        ->getCollection(),
                ]);

        }
        return 'block';
    }
}