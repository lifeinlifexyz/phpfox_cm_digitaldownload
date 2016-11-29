<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


use Apps\CM_DigitalDownload\Lib\Form\Field\Factory;

class Filter
{
    protected $aFields;
    protected $oSearch;
    protected $oFactory;

    public function __construct()
    {
        $this->oFactory = new Factory();
    }

    public function setFields($aFields)
    {
        $this->aFields = $aFields;
        return $this;
    }

    public function setSearcher($oSearch)
    {
        $this->oSearch = $oSearch;
        return $this;
    }

    public function getSearchParams($sTableAlias)
    {
        $aFields = $this->aFields;
        $aFilters = [];

        foreach($aFields as &$aField) {
            $oType = $this->oFactory->createType($aField['type'], [
                'name' => $aField['name'],
                'title' => _p($aField['caption_phrase'])
            ]);
            $mFilter = $oType->getFilter($sTableAlias);
            if (!is_array($mFilter)) {
                $sFieldName = $mFilter['field_name'];
                unset($mFilter['field_name']);
                $aFilters[$sFieldName] = $mFilter;
            }
        }

        return  [
            'type' => 'browse',
            'filters' => $aFilters,
            'search' => 'title',
            'custom_search' => true
        ];
    }
    /**
     * @param Factory $oFactory
     * @return Filter
     */
    public function setFactory($oFactory)
    {
        $this->oFactory = $oFactory;
        return $this;
    }
}