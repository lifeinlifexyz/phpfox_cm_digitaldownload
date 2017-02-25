<?php


namespace Apps\CM_DigitalDownload\Block;


class MostDownloaded extends \Phpfox_Component
{
    public function process()
    {

        $this->template()
            ->assign([
                'aDDs' =>  \Phpfox::getService('digitaldownload.browse')
                    ->conditions([
                        ' AND `d`.`is_active` = 1',
                        ' AND `d`.`total_download` > 0'
                    ])
                    ->limit(\Phpfox::getParam('cm_dd_most_downloaded_limit', 4))
                    ->page(1)
                    ->sort('`d`.`total_download` DESC')
                    ->getCollection(false),
            ]);

        return 'block';
    }
}