<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;

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
        return [
            'parent_id' => [
                'type' => 'tree',
                'name' => 'parent_id',
                'parent' => 'parent_id',
                'key' => 'category_id',
                'title' => _p('Parent'),
                'translate' => true,
                'items' => $this->getList(),
                'filter' => function ($sValue) {
                    return (int)$sValue;
                }
            ],
            'name' => [
                'type' => 'mstring',
                'name' => 'name',
//                'module' => 'digitaldownload',
                'title' => _p('Name'),
                'rules' => 'required',
            ],
            'is_active' => [
                'type' => 'boolean',
                'name' => 'is_active',
                'title' => _p('Active'),
                'rules' => '0:1:in',
                'filter' => function ($sValue) {
                    return (int)$sValue;
                }
            ],
        ];
    }

    public function getList()
    {
        return $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->order("`ordering` ASC")
            ->execute('getslaverows');
    }

    public function all()
    {
        $aList = $this->getList();
        $aResult = [];
        foreach ($aList as &$aRow) {
            $aResult[$aRow['parent_id']][] = $aRow;
        }
        return $aResult;
    }

    /**
     * activae/deactivate category by id
     * @param $iStatus integer
     * @param $iId integer
     * @return bool
     */
    public function setStatus($iStatus, $iId)
    {
        return $this->database()->update(\Phpfox::getT($this->_sTable),
            ['`is_active`' => $iStatus], '`category_id`=' . $iId);
    }

}