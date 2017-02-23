<?php


namespace Apps\CM_DigitalDownload\Block;


class Featured extends \Phpfox_Component
{
    public function process()
    {
        $aDDs = \Phpfox::getService('digitaldownload.browse')
            ->conditions([
                'AND `d`.`featured` = 1',
                ' AND `d`.`is_active` = 1'
            ])
            ->limit(\Phpfox::getParam('cm_dd_feature_limit', 4))
            ->page(1)
            ->sort(' RAND()')
            ->getCollection(false);
        if (!(count($aDDs) > 0)) {
            return 'block';
        }

        $this->template()
            ->assign([
                'sHeader' => _p('Featured'),
                'aDDs' =>  $aDDs,
            ]);

        return 'block';
    }
}