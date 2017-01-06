<?php
namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FilterTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;

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

        $aFields['price'] = [
            'type' => 'price',
            'name' => 'price',
            'title' => _p('Price'),
            'table_alias' => 'd',
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
            $this->aAttr =  $this->database()
                ->select('*')
                ->from(\Phpfox::getT($this->_sTable))
                ->where('`id` = ' . $this->mKey)
                ->execute('getRow');
            $this->iCategoryId = $this->aAttr['category_id'];
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
            'rules' => 'required',
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

    public function getDisplayer($iId)
    {
        $iId = (int) $iId;

        $oDisplay = new Display($this);
        //todo::save row to cache;
        $aRow = $this->database()
            ->select('d.*')
            ->from(\Phpfox::getT($this->_sTable), 'd')
            ->where('id = ' . $iId)
            ->get();

        $oDisplay->setRow($aRow);
        return $oDisplay;
    }

    public function updateById($iID, $aVal)
    {
        return $this->database()->update(\Phpfox::getT($this->_sTable), $aVal, '`id` = ' . $iID);
    }

}