<?php
namespace Apps\CM_DigitalDownload\Service\Digitaldownload;

use Apps\CM_DigitalDownload\Lib\Cache\CMCache;
use Apps\CM_DigitalDownload\Lib\Exception\ServiceException;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FilterTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Form\Exception\RequiredArgumentException;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;
use Apps\CM_DigitalDownload\Lib\CustomFieldType\DigitalDownload as DDField;
use Apps\CM_DigitalDownload\Service\Plan;
use Phpfox;
use Phpfox_Plugin;

class DigitalDownload  extends \Phpfox_Service implements IFormly
{
    use FormlyTrait;
    use FilterTrait;

    protected $_sTable = 'digital_download';
    protected $sKeyName = 'id';
    protected $iCategoryId = null;
    /**
     * @var Tree
     */
    protected $oTreeManager;

    private $_aDisplayer = [];

    public function __construct()
    {
        $this->oTreeManager = new Tree();
    }

    public function getFilterFields()
    {
        $aSearch = request()->getArray('search');

        $iCategoryId = isset($aSearch['category_id']) ? $aSearch['category_id'] : 0;
        $aFields = [];
        $aFields['category_id'] = $this->getCategoryFieldData();
        $aFields['category_id']['template'] = '@CM_DigitalDownload/filter/fields/category.html';
        $aFields['category_id']['tree_option_tmp'] = '@CM_DigitalDownload/filter/fields/tree-option.html';

        $aCategoryIds = $this->oTreeManager->getAllChildValues($aFields['category_id']['items'], $iCategoryId, [$iCategoryId]);

        $aRawFields =\Phpfox::getService('digitaldownload.field')->getFilterable($aCategoryIds);
        foreach($aRawFields as &$aRawField) {
            $aFields[$aRawField['name']] = $this->buildFieldInfo($aRawField, true);
        }

        $aDDFields = \Phpfox::getService('digitaldownload.field')->getFieldsByType('dd');
        $sMinMaxSelect  = '';
        foreach($aDDFields as &$sDDField) {
            $sMinMaxSelect .= 'MIN(`d`.`' . $sDDField . '_price`) as `' . $sDDField .'_min`, MAX(`d`.`' . $sDDField . '_price`) as `' . $sDDField .'_max`,';
        }
        $sMinMaxSelect  = rtrim($sMinMaxSelect, ',');
        $aMinMax = $this->database()
            ->select($sMinMaxSelect)
            ->from(Phpfox::getT($this->_sTable), 'd')
            ->where('`category_id` in (' . implode(', ', $aCategoryIds) . ') AND `is_active` = 1')
            ->get();
        $iMin = 0;
        $iMax = 0;
        foreach($aMinMax as $sKey => $fValue) {
            if (strpos($sKey, '_min') && $fValue < $iMin) {
                $iMin = $fValue;
            } elseif (strpos($sKey, '_max') && $fValue > $iMax) {
                $iMax = $fValue;
            }
        }
        $iMin = round($iMin);
        $iMax = round($iMax);
        if ($iMax > 0 && $iMin != $iMax) {
            $aFields['price'] = [
                'type' => 'dd_price',
                'is_search' => true,
                'name' => 'price',
                'title' => _p('Price'),
                'table_alias' => 'd',
                'template' => '@CM_DigitalDownload/filter/fields/slider.html',
                'table' => \Phpfox::getT($this->_sTable),
                'min' => $iMin,
                'max' => $iMax,
                'columns' => $aDDFields,
                'column' => 'price',
            ];
        }

        return $aFields;
    }

    public function getCategoryFieldData($aItems = null)
    {
        return CMCache::remember('cm_dd_category_data', function() use ($aItems) {
              return
                  [
                      'type' => 'tree',
                      'name' => 'category_id',
                      'parent' => 'parent_id',
                      'key' => 'category_id',
                      'title' => _p('Category'),
                      'translate' => true,
                      'items' => is_null($aItems) ? \Phpfox::getService('digitaldownload.category')->getActive(true) : $aItems,
                      'table_alias' => 'd',
                  ];
        });
    }

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        if ($this->mKey) {
            $this->aRow =  $this->getForEdit($this->mKey);
            $this->iCategoryId = $this->aRow['category_id'];
        }
        if (!$this->iCategoryId) {
            throw new \InvalidArgumentException('Category id is null');
        }
        $that = $this;
        return CMCache::remember('cm_dd_category_fields_' . $this->iCategoryId, function() use ($that) {
            $aRawFields =\Phpfox::getService('digitaldownload.categoryField')->getInfoByCategoryId($this->iCategoryId);
            $aFields = [];

            foreach($aRawFields as &$aRawField) {
                $aFields[$aRawField['name']] = $this->buildFieldInfo($aRawField);
            }

            //add system fields
            $aFields['category_id'] = [
                'type' => 'hidden',
                'name' => 'category_id',
                'title' => '',
                'value' => $this->iCategoryId,
            ];

            $aFields['privacy'] = [
                'type' => 'privacy',
                'name' => 'privacy',
                'title' => _p('Digital download privacy'),
                'value' => 0,
            ];
            return $aFields;
        }, 0, 'cm_dd_category_fields');
    }

    protected function buildFieldInfo($aRawField, $bFilter = false)
    {
        //todo:: build field info array certain field type. it is simple type with out extra data
        $aRes =  [
            'type' => $aRawField['type'],
            'name' => $aRawField['name'],
            'title' => _p($aRawField['caption_phrase']),
            'rules' => !empty($aRawField['rules']) ?  $aRawField['rules']: null,
        ];

        if ($bFilter) {
            $aRes['template'] = '@CM_DigitalDownload/filter/fields/' . $aRawField['type'] . '.html';
            $aRes['table_alias'] = 'd';
        }

        return $aRes;
    }

    /**
     * @param $iCategoryId
     * @return $this
     */
    public function setCategoryId($iCategoryId)
    {
        $this->iCategoryId = $iCategoryId;
        return $this;
    }

    public function getDisplayer($iId)
    {
        $iId = (int) $iId;
        if (!isset($this->_aDisplayer[$iId])) {
            $oDisplay = new Display($this);

            if (is_null($this->aRow)) {
                (($sPlugin = Phpfox_Plugin::get('digitaldownload.service_digitaldownload_getdisplayer')) ? eval($sPlugin) : false);
                $this->aRow =  $this->getForFeed($iId);
            }

            if (empty($this->aRow)) {
                throw new ServiceException('Row data not set');
            }

            $oDisplay->setRow($this->aRow);
            $this->_aDisplayer[$iId] = $oDisplay;
        }
        return $this->_aDisplayer[$iId];
    }

    public function getForFeed($iId)
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'digitaldownload\' AND lik.item_id = d.id AND lik.user_id = ' . Phpfox::getUserId());
        }

        $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = d.user_id AND f.friend_user_id = " . Phpfox::getUserId());

       return $this->database()
            ->select(Phpfox::getUserField() . ',u.*, uf.total_score, uf.total_rating, ua.activity_points, di.*,   d.*')
            ->from(Phpfox::getT($this->_sTable), 'd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = d.user_id')
            ->join(Phpfox::getT('user_activity'), 'ua', 'ua.user_id = d.user_id')
            ->leftJoin(Phpfox::getT('digital_download_invite'), 'di', 'di.dd_id = d.id AND di.invited_user_id = ' . Phpfox::getUserId())
            ->where('id = ' . (int)$iId)
            ->get();
    }

    public function updateById($iID, $aVal)
    {
        return $this->database()->update(\Phpfox::getT($this->_sTable), $aVal, '`id` = ' . $iID);
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()
            ->select('`d`.*, `p`.`info` as plan_info')
            ->from(\Phpfox::getT($this->_sTable), 'd')
            ->leftJoin(\Phpfox::getT(Plan::DD_PLAN_TABLE), 'p', '`d`.`id` = `p`.`dd_id`')
            ->where('`d` .`id` = ' . $iId)
            ->get();

        return $aRow;
    }

    public function updateParsedTitle($oDD)
    {
        $this->updateById($oDD['id'], ['_title' => (string)$oDD]);
    }

    public function activate($iId, array $aPlan = [])
    {
        $iId = (int) $iId;

        $oDD = $this->getDisplayer($iId);

        $aVal = [
            'is_active' => '1',
            'is_expired' => '0',
        ];

        if ($oDD['expire_timestamp'] <= PHPFOX_TIME) {

            if (!(count($aPlan) > 0)) {
                $aPlan = json_encode($this->database()
                    ->select('`info`')
                    ->from(\Phpfox::getT(Plan::DD_PLAN_TABLE))
                    ->where('`dd_id` = ' . $iId)
                    ->get(), true);
            }

            $iLifeDays = (!isset($aPlan['life_time']) || $aPlan['life_time'] == 0)
                ? 365 * 10
                : $aPlan['life_time'];
            $aVal['expire_timestamp'] = PHPFOX_TIME + 60 * 60 * 24 * $iLifeDays;
        }

        $this->updateById($iId, $aVal);

        //insert new feed
        \Phpfox::getService('feed.process')->add('digitaldownload', $iId, $oDD['privacy'], 0);
        $this->updateParsedTitle($oDD);

        $oDD['is_active'] = true;
        return  $this;
    }

    public function activateByModerator($iId) {
        $iId = (int) $iId;

        $oDD = $this->getDisplayer($iId);

        $aVal = [
            'is_active' => '1',
            'is_expired' => '0',
        ];

        if ($oDD['expire_timestamp'] <= PHPFOX_TIME) {
            $iLifeDays = 365 * 10;
            $aVal['expire_timestamp'] = PHPFOX_TIME + 60 * 60 * 24 * $iLifeDays;
        }

        $this->updateById($iId, $aVal);

        //insert new feed
        \Phpfox::getService('feed.process')->add('digitaldownload', $iId, $oDD['privacy'], 0);
        $this->updateParsedTitle($oDD);

        $oDD['is_active'] = true;
        return  $this;
    }

    public function deactivate($iId)
    {
        $this->updateById($iId, ['is_active' => 0]);
        //delete feed
        \Phpfox::getService('feed.process')->delete('digitaldownload', $iId);
        return  $this;
    }

    public function delete($iId)
    {
        try {
            $this->database()->beginTransaction();

            $iId = (int)$iId;
            $oDD = $this->getDisplayer($iId);
            (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_dd_delete')) ? eval($sPlugin) : false);

            Phpfox::getService('digitaldownload.images')->deleteDDImages($iId);


            $aDDFields = Phpfox::getService('digitaldownload.field')->getFieldsByType('dd');
            try {
                foreach($aDDFields as $sDDField) {
                    $oField = $oDD->getField($sDDField);
                    if ($oField instanceof  DDField) {
                        $oField->delete();
                    }
                }
            }catch(RequiredArgumentException $e) {
                Phpfox::log('Not set dd type');
            }
            $this->database()->delete(Phpfox::getT($this->_sTable), '`id` = ' . $iId);
            $this->database()->delete(Phpfox::getT(Plan::DD_PLAN_TABLE), '`dd_id` = ' . $iId);

            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $iId, 'digitaldownload') : null);
            //delete feed
            \Phpfox::getService('feed.process')->delete('digitaldownload', $iId);
            \Phpfox::getService('feed.process')->delete('digitaldownload_comment', $iId);

            //delete notify
//            \Phpfox::getService('notification.process')->delete('digitaldownload', $iId);
//            \Phpfox::getService('notification.process')->delete('digitaldownload_like', $iId);

            Phpfox::massCallback('deleteItem', [
                'sModule' => 'digitaldownload',
                'sTable' => Phpfox::getT('digitaldownload'),
                'iItemId' => $iId
            ]);

            (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_dd_delete')) ? eval($sPlugin) : false);
            $this->database()->commit();

            (($sPlugin = Phpfox_Plugin::get('digitaldownload.after_dd_delete')) ? eval($sPlugin) : false);

            return $this;
        } catch (\Exception $e) {
            $this->database()->rollback();
            throw  $e;
        }

    }

    public function getPlan($iId) {
        $aPlan = $this->database()
            ->select('*')
            ->from(Phpfox::getT(Plan::DD_PLAN_TABLE))
            ->where('`dd_id` = ' . (int) $iId)
            ->get();
        $aPlan = json_decode($aPlan['info'], true);
        return $aPlan;
    }

}