<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;

Interface IDataBinder
{
    /**
     * @param string $mService
     * @param null|string|int $mKey
     * @return IForm
     */
    public function getBindedForm($mService, $mKey = null, array $aFormData = [], array $aServiceParams = []);
}