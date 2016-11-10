<?php

namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;

interface IForm
{
    /**
     * @return $this
     */
    public function save();

    /**
     * @return boolean
     */
    public function isValid();
}