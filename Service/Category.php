<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Cache\CMCache;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Phpfox_Plugin;

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
                'module' => 'digitaldownload',
                'title' => _p('Name'),
                'rules' => 'required',
            ],
            'title' => [
                'type' => 'string',
                'value' => '$title',
                'name' => 'title',
                'title' => _p('Title for item'),
            ],
            'keywords' => [
                'type' => 'string',
                'name' => 'keywords',
                'title' => _p('SEO Keywords for item'),
            ],
            'description' => [
                'type' => 'text',
                'name' => 'description',
                'title' => _p('SEO Description for item'),
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

    public function getActive($bRaw = false)
    {
        $aList = $this->database()
            ->select("*")
            ->from(\Phpfox::getT($this->_sTable))
            ->where('is_active=1')
            ->order("`ordering` ASC")
            ->execute('getslaverows');
        if ($bRaw) {
            return $aList;
        }
        $aResult = [];
        foreach ($aList as &$aRow) {
            $aResult[$aRow['parent_id']][] = $aRow;
        }

        return $aResult;
    }

    /**
     * activae/deactivate category by ids
     * @param $iStatus integer
     * @param $iIds array
     * @return bool
     */
    public function setStatus($iStatus, $iIds)
    {
        $iIds = (array)$iIds;
        CMCache::remove('cm_dd_category_data');
        return $this->database()->update(\Phpfox::getT($this->_sTable),
            ['`is_active`' => $iStatus], '`category_id` in (' . implode(',', $iIds) . ')');
    }


    /**
     * @param array $aOrders
     * @return $this
     */
    public function order($aOrders = [])
    {
        foreach($aOrders as $iKey => $aOrder) {
            $aData = [
                'parent_id' => $aOrder['parent_id'],
                'ordering' => $iKey,
            ];
            $this->database()->update(\Phpfox::getT($this->_sTable), $aData, '`category_id` = ' . $aOrder['id']);
        }
        CMCache::remove('cm_dd_category_data');
        return $this;
    }

    public function delete($iId)
    {
        $this->database()->delete(\Phpfox::getT($this->_sTable),  '`category_id` = ' . $iId);
        \Phpfox::getService('digitaldownload.categoryField')->delete($iId);
        CMCache::remove('cm_dd_category_data');
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.service_category_after_delete')) ? eval($sPlugin) : false);
    }

}