<?php


namespace Apps\CM_DigitalDownload\Block;


class MostLiked extends \Phpfox_Component
{
    public function process()
    {

        $this->template()
            ->assign([
                'aDDs' =>  \Phpfox::getService('digitaldownload.browse')
                    ->conditions([
                        ' AND `d`.`is_active` = 1'
                    ])
                    ->limit(\Phpfox::getParam('cm_dd_most_liked_limit', 4))
                    ->page(1)
                    ->sort('`d`.`total_like` DESC')
                    ->getCollection(false),
            ]);

        return 'block';
    }
}