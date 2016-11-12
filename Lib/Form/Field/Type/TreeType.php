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
    ];


    public function __construct(array $aData)
    {
        parent::__construct($aData);
    }

    protected function getVars()
    {
        $aTmp = [];
        $sParentField = $this->aInfo['parent_field'];

        foreach ($this->aInfo['items'] as $aRow) {
            $aTmp[$aRow[$sParentField]][] = $aRow;
        }

        $this->aInfo['tree_values'] = $aTmp;

        return parent::getVars();
    }

    /**
     * @param Tree $oTree
     * @return SelectType
     */
    public function setTree($oTree)
    {
        $this->oTree = $oTree;
        return $this;
    }


}