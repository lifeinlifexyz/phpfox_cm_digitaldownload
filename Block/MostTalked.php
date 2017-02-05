<?php


namespace Apps\CM_DigitalDownload\Block;


class MostTalked extends \Phpfox_Component
{
    public function process()
    {

        $this->template()
            ->assign([
                'aDDs' =>  \Phpfox::getService('digitaldownload.browse')
                    ->conditions([
                        ' AND `d`.`is_active` = 1'
                    ])
                    ->limit(\Phpfox::getParam('cm_dd_most_talked_limit', 4))
                    ->page(1)
                    ->sort('`d`.`total_comment` DESC')
                    ->getCollection(false),
            ]);

        return 'block';
    }
}