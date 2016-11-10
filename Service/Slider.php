<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;

class Slider extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $sKeyName = 'slide_id';
    protected $_sTable = 'ynclean_slide';

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        return [
            'title' => [
                'type' => 'string',
                'name' => 'title',
                'title' => 'Title',
                'filter' => [$this, 'filter'],
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

    public function filter($text)
    {
        return strip_tags($text);
    }
}