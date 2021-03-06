<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;

class TreeType extends AbstractType
{

    /**
     * @var Tree
     */
    protected $oTree;
    protected $sConditionValue = null;

    protected $aInfo = [
        'template' => '@CM_DigitalDownload/form/fields/tree.html',
        'tree_option_tmp' => '@CM_DigitalDownload/form/fields/tree-option.html',
        'title_field' => 'name',
        'parent_field' => 'parent_id',
        'key_field' => 'category_id',
        'root_id' => 0,
        'display_seperator' => '/',
    ];


    public function __construct(array $aData)
    {
        parent::__construct($aData);

        $this->oTree = isset($aData['tree_manager'])
            ? $aData['tree_manager']
            : new Tree();

        $this->oTree
            ->setParentField($this->aInfo['parent_field'])
            ->setTitleField($this->aInfo['title_field'])
            ->setKeyField($this->aInfo['key_field'])
            ->setRootId($this->aInfo['root_id']);

    }

    public function setConditionValue($mConditionValue)
    {
        $sConditionValue = is_array($mConditionValue) ? '(' . implode(', ', $mConditionValue) . ')' : $mConditionValue;
        $this->sConditionValue = $sConditionValue;
        return $this;
    }

    protected function getVars()
    {
        $this->aInfo['tree_values'] = $this->oTree->build($this->aInfo['items']);
        return parent::getVars();
    }

    public function getDisplay()
    {
        $iValue = $this->getValue();
        if (is_null($iValue)) {
            return '';
        }

        $aBranches = $this->oTree->parents($this->aInfo['items'], $iValue);
        $sTitleField = $this->aInfo['title_field'];
        $sSeperator = $this->aInfo['display_seperator'];

        $sRes = '';
        foreach($aBranches as &$aItem) {
            $sRes .= _p($aItem[$sTitleField]) . $sSeperator;
        }
        return rtrim($sRes, $sSeperator);
    }

    public function getValueArray()
    {
        return $this->oTree->getValueArray($this->aInfo['items'], $this->getValue());
    }

    public function setCondition(\Phpfox_Search &$oSearch, $aSearch)
    {
        $sKey = $this->aInfo['column'];
        $sTAlias = $this->aInfo['table_alias'];
        if (($sValue = $oSearch->get($sKey)) || (isset($aSearch[$sKey]) && $sValue = $aSearch[$sKey])) {
            $oSearch->setCondition('AND `' . $sTAlias . '`.`' . $sKey . '` IN ' . $this->getConditionValue($sValue));
        }
    }

    public function getConditionValue($sValue)
    {
        if (is_null($this->sConditionValue)) {
            $aCondValue = $this->oTree->getAllChildValues($this->aInfo['items'], $sValue, [$sValue]);
            $this->setConditionValue($aCondValue);
        }
        return $this->sConditionValue;

    }
}