<?php
namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


use Apps\CM_DigitalDownload\Lib\Form\Field\AbstractType;
use Apps\CM_DigitalDownload\Lib\Form\Form;
use Core\Event;
use Core\Search;
use Core\View;
use Phpfox_Search;

class FilterForm extends Form
{
    /**
     * @var Phpfox_Search
     */
    protected $oSearch;
    /**
     * @param array $aData - field data
     * @return  $this
     */
    public function addField($sType, $aData)
    {
        $sName = $aData['name'];
        /**
         * @var $oType AbstractType
         */
        $aData['name'] = 'search[' . $sName . ']';
        $aData['column'] = $sName;
        $oType = $this->oFactory->createType($sType, $aData);
        $oType->setView($this->oView);
        $this->aFields[$sName] = $oType;
        return $this;
    }

    public function render($sTPL = '@CM_DigitalDownload/filter/form.html')
    {
        try {
            return parent::render($sTPL);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param Phpfox_Search $oSearch
     * @return FilterForm
     */
    public function setSearch($oSearch)
    {
        $this->oSearch = $oSearch;
        return $this;
    }

    /**
     * @return Phpfox_Search
     */
    public function defineConditions()
    {
        $aFields = $this->aFields;
        $aSearch = request()->get('search');
        foreach($aFields as $sName => &$oField) {
            /**
             * @var $oField AbstractType
             */
            $oField->setCondition($this->oSearch, $aSearch);
        }
        return $this->oSearch;
    }
}