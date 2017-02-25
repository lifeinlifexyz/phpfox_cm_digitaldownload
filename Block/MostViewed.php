<?php


namespace Apps\CM_DigitalDownload\Block;


class MostViewed extends \Phpfox_Component
{
    public function process()
    {

        $this->template()
            ->assign([
                'aDDs' =>  \Phpfox::getService('digitaldownload.browse')
                    ->conditions([
                        ' AND `d`.`is_active` = 1',
                        ' AND `d`.`total_view` > 0',
                    ])
                    ->limit(\Phpfox::getParam('cm_dd_most_viewed_limit', 4))
                    ->page(1)
                    ->sort('`d`.`total_view` DESC')
                    ->getCollection(false),
            ]);

        return 'block';
    }
}