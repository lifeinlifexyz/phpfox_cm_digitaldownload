<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;

class Category extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;

    protected $_sTable = 'digital_download_category';
    protected $sKeyName = 'category_id';

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {

        $aCats =  $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->execute('getslaverows');

        $aCatItems = [];

        foreach($aCats as $aCat) {
            $aCatItems[$aCat[$this->sKeyName]] = $aCat['name'];
        }

        return [
            'parent_id'  => [
                'type' => 'select',
                'name' => 'parent_id',
                'title' => _p('Parent'),
                'items' => $aCatItems,
                'filter' => function($sValue) {
                    return (int) $sValue;
                }
            ],
            'name'  => [
                'type' => 'multiLangString',
                'name' => 'name',
                'title' => _p('Name'),
                'rules' => 'required',
            ],
            'is_active'  => [
                'type' => 'boolean',
                'name' => 'is_active',
                'title' => _p('Active'),
                'rules' => '0:1:in',
                'filter' => function($sValue) {
                    return (int) $sValue;
                }
            ],
        ];
    }

}