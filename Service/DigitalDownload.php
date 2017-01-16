<?php
namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FilterTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;
use Core\Event;
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
        //todo:: save to cache category fields
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
        event('before_get_min_max_price', function($aCond) use ($aCategoryIds) {
           return $aCond[]  = 'AND `price` in (' . implode(', ', $aCategoryIds) . ')';
        });
        $aFields['price'] = [
            'type' => 'price',
            'is_search' => true,
            'name' => 'price',
            'title' => _p('Price'),
            'table_alias' => 'd',
            'template' => '@CM_DigitalDownload/filter/fields/slider.html',
            'table' => \Phpfox::getT($this->_sTable),
        ];

        return $aFields;
    }

    public function getCategoryFieldData($aItems = null)
    {
        return [
            'type' => 'tree',
            'name' => 'category_id',
            'parent' => 'parent_id',
            'key' => 'category_id',
            'title' => _p('Category'),
            'translate' => true,
            'items' => is_null($aItems) ? \Phpfox::getService('digitaldownload.category')->getActive(true) : $aItems,
            'table_alias' => 'd',
        ];
    }

    /**
     * return array of fields info
     * @return array
     */
    public function getFieldsInfo()
    {
        if ($this->mKey) {
            $this->aRow =  $this->database()
                ->select('*')
                ->from(\Phpfox::getT($this->_sTable))
                ->where('`id` = ' . $this->mKey)
                ->get();
            $this->iCategoryId = $this->aRow['category_id'];
        }
        if (!$this->iCategoryId) {
            throw new \InvalidArgumentException('Category id is null');
        }
        //todo:: save to cache category fields
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

        $aFields['price'] = [
            'type' => 'price',
            'name' => 'price',
            'title' => _p('Price'),
        ];

        $aFields['digital_download'] = [
            'type' => 'file',
            'name' => 'digital_download',
            'title' => _p('Digital download'),
            'dir' => PHPFOX_DIR_FILE . 'digital_download' . PHPFOX_DS,
//            'rules' => 'required',
        ];

        $aFields['privacy'] = [
            'type' => 'privacy',
            'name' => 'privacy',
            'title' => _p('Digital download privacy'),
            'value' => 0,
        ];

        return $aFields;
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

    public function &getDisplayer($iId)
    {
        $iId = (int) $iId;
        if (!isset($this->_aDisplayer[$iId])) {
            $oDisplay = new Display($this);

            if (is_null($this->aRow)) {

                (($sPlugin = Phpfox_Plugin::get('digitaldownload.service_digitaldownload_getdisplayer')) ? eval($sPlugin) : false);

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'digitaldownload\' AND lik.item_id = d.id AND lik.user_id = ' . Phpfox::getUserId());
                }

                $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = d.user_id AND f.friend_user_id = " . Phpfox::getUserId());

                $this->aRow =  $this->database()
                    ->select(Phpfox::getUserField() . ', d.*, u.*, uf.total_score, uf.total_rating, ua.activity_points')
                    ->from(Phpfox::getT($this->_sTable), 'd')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                    ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = d.user_id')
                    ->join(Phpfox::getT('user_activity'), 'ua', 'ua.user_id = d.user_id')
                    ->where('id = ' . $iId)
                    ->get();
            }

            $oDisplay->setRow($this->aRow);
            $this->_aDisplayer[$iId] = $oDisplay;
        }

        return $this->_aDisplayer[$iId];
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

    public function activate($iId, array $aPlan = [])
    {
        if (!(count($aPlan) > 0)) {
            $aPlan = json_decode($this->database()
                ->select('`info`')
                ->from(\Phpfox::getT(Plan::DD_PLAN_TABLE))
                ->where('`dd_id` = ' . $iId)
                ->get(), true);
        }

        $oDD = $this->getDisplayer($iId);

        $aVal = [
            'is_active' => true
        ];

        if ($oDD['expire_timestamp'] <= PHPFOX_TIME) {
            $aVal['expire_timestamp'] = PHPFOX_TIME + 60 * 60 * 24 * $aPlan['life_time'];
        }

        $this->updateById($iId, $aVal);
        $oDD['is_active'] = true;
    }

}