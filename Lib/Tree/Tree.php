<?php
namespace Apps\CM_DigitalDownload\Lib\Tree;

class Tree
{
    protected $sParentField;
    protected $sTitleField;
    protected $sKeyField;
    protected $iRootId = 0;

    public function setOptions(array $aOption)
    {
        foreach($aOption as $sOptionName => $mValue) {
            if (property_exists($this, $sOptionName)) {
                $this->{$sOptionName} = $mValue;
            }
        }
        return $this;
    }

    public function build($aItems)
    {
        $aList = [];
        foreach ($aItems as $aRow) {
            $aList[$aRow[$this->sParentField]][] = $aRow;
        }
        return $this->buildTree($aList);
    }

    public function parents($aItems, $mValue)
    {
        $aValue = $this->getNode($aItems, $mValue, $this->sKeyField);
        $aNode = $aValue;
        $aBranches = [];
        while(isset($aNode[$this->sParentField])) //walk to root
        {
            $aNode = $this->getNode($aItems, $aNode[$this->sParentField], $this->sKeyField);
            if (!empty($aNode)) {
                $aBranches[] = $aNode;
            }
        }
        $aBranches = array_reverse($aBranches);
        $aBranches[] = $aValue;
        return $aBranches;
    }

    public function getValueArray($aItems, $iValue)
    {
        return $this->getNode($aItems, $iValue, $this->sKeyField);
    }

    public function childs()
    {

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

    private function buildTree(&$aList, $pid = 0, $iLevel = 0)
    {
        $aTree = [];

        if (!isset($aList[$pid])) {
            return $aTree;
        }

        foreach ($aList[$pid] as $aChild)
        {
            $aChild['level'] = $iLevel;
            $aChilds =  $this->buildTree($aList, $aChild[$this->sKeyField], $iLevel);
            if ($aChilds) {
                $iLevel++;
                $aChild['childs'] = $aChilds;
            }
            $aTree[] = $aChild;
        }
        return $aTree;
    }

    /**
     * @param mixed $sParentField
     * @return Tree
     */
    public function setParentField($sParentField)
    {
        $this->sParentField = $sParentField;
        return $this;
    }

    /**
     * @param mixed $sTitleField
     * @return Tree
     */
    public function setTitleField($sTitleField)
    {
        $this->sTitleField = $sTitleField;
        return $this;
    }

    /**
     * @param mixed $sKeyField
     * @return Tree
     */
    public function setKeyField($sKeyField)
    {
        $this->sKeyField = $sKeyField;
        return $this;
    }

    /**
     * @param int $iRootId
     * @return Tree
     */
    public function setRootId($iRootId)
    {
        $this->iRootId = $iRootId;
        return $this;
    }

}