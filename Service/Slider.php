<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\DataInfoTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IDataInfo;

class Slider extends \Phpfox_Service implements IDataInfo
{
    use DataInfoTrait;

    protected $sKeyName = 'slide_id';
    protected $_sTable = 'ynclean_slide';

    protected $aFieldsInfo  = [
        'title' => [
            'type' => 'string',
            'name' => 'title',
            'title' => 'Title',
            'rules' => 'required|3:250:length',
        ],
        'description' => [
            'type' => 'string',
            'name' => 'description',
            'title' => 'Description',
            'rules' => 'required|3:500:length',
        ]
    ];

}