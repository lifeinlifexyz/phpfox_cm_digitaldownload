<?php


namespace Apps\CM_DigitalDownload\Block;


class Filter extends \Phpfox_Component
{
    public function process()
    {
        $this->template()
            ->assign([
                    'sHeader' => _p('Filter'),
            ]);

        return 'block';
    }
}