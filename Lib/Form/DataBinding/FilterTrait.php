<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;

trait FilterTrait
{
    public function getFilterForm(array $aData = [])
    {
        $oBuilder = $this->getFormBuilder();
        $aFields = $this->getFilterFields();

        /**
         * @var $oForm Form
         */
        $oForm = $oBuilder->buildFilterForm($aFields, $aData);
        return $oForm;
    }
}