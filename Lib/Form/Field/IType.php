<?php

namespace Apps\CM_DigitalDownload\Lib\Form\Field;

interface IType
{
    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string mixed - html markup
     */
    public function render();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @param $sTpl
     * @return $this
     */
    public function setTemplate($sTpl);

    /**
     * @return array
     */
    public function getColumnDefinitions();
}