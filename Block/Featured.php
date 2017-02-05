<?php


namespace Apps\CM_DigitalDownload\Block;


class Featured extends \Phpfox_Component
{
    public function process()
    {
        $this->template()
            ->assign([
                'sHeader' => _p('Featured'),
                'aDDs' =>  \Phpfox::getService('digitaldownload.browse')
                    ->conditions([
                        'AND `d`.`featured` = 1',
                        ' AND `d`.`is_active` = 1'
                    ])
                    ->limit(\Phpfox::getParam('cm_dd_feature_limit', 4))
                    ->page(1)
                    ->sort(' RAND()')
                    ->getCollection(false),
            ]);

        return 'block';
    }
}