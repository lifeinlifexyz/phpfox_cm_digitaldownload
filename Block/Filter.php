<?php


namespace Apps\CM_DigitalDownload\Block;


class Filter extends \Phpfox_Component
{
    public function process()
    {
        $this->template()->assign('oFilterForm', \Phpfox::getService('digitaldownload.dd')->getFilterForm());
        $this->template()->assign([
            'sHeader' => _p('Find')
        ]);
        return 'block';
    }
}