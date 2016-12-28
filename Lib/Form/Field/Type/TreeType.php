<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field\Type;

use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;

class TreeType extends AbstractType
{

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
    }

    protected function getVars()
    {
        $aList = [];
        $sParentField = $this->aInfo['parent_field'];

        foreach ($this->aInfo['items'] as $aRow) {
            $aList[$aRow[$sParentField]][] = $aRow;
        }

        $aTree = $this->buildTree($aList);
        $this->aInfo['tree_values'] = $aTree;

        return parent::getVars();
    }

    private function buildTree(&$aList, $pid = 0, $iLevel = 0)
    {
        $aTree = [];

        if (!isset($aList[$pid])) {
            return $aTree;
        }

        foreach ($aList[$pid] as $aChild)
        {
            $aChild['level'] = $iLevel;
            $aChilds =  $this->buildTree($aList, $aChild[$this->aInfo['key_field']], $iLevel);
            if ($aChilds) {
                $iLevel++;
                $aChild['childs'] = $aChilds;
            }
            $aTree[] = $aChild;
        }
        return $aTree;
    }

    public function getFilter($sTableAlias)
    {
        $aInfo = $this->aInfo;
        return [
            'type' => 'input:text',
            'field_name' => $aInfo['name'],
            'size' => 17,
        ];
    }

    public function getDisplay()
    {
        $iValue = $this->getValue();
        if (is_null($iValue)) {
            return '';
        }
        $aItems = $this->aInfo['items'];
        $aValue = $this->getNode($aItems, $iValue, $this->aInfo['key_field']);
        $aNode = $aValue;
        $sPField = $this->aInfo['parent_field'];
        $sSeperator = $this->aInfo['display_seperator'];
        $sTitleField = $this->aInfo['title_field'];
        $aBranches = [];
        while(isset($aNode[$sPField])) //walk to root
        {
            $aNode = $this->getNode($aItems, $aNode[$sPField], $this->aInfo['key_field']);
            if (!empty($aNode)) {
                $aBranches[] = $aNode;
            }
        }
        $aBranches = array_reverse($aBranches);
        $sRes = '';
        foreach($aBranches as $aItem) {
            $sRes .= _p($aItem[$sTitleField]) . $sSeperator;
        }
        $sRes .= _p($aValue[$sTitleField]);
        return $sRes;
    }

    protected function getNode($aItems, $mValue, $sKey)
    {
        $aRes = [];
        foreach($aItems as &$aItem) {
            if ($aItem[$sKey] == $mValue) {
                $aRes = $aItem;
                break;
            }
        }
        return  $aRes;
    }

}