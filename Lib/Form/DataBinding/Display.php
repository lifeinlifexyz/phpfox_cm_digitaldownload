<?php
namespace Apps\CM_DigitalDownload\Lib\Form\DataBinding;


class Display implements \ArrayAccess
{
    protected $oForm;
    protected $aRow;
    protected $cToStrCallback = null;

    public function __construct(Form $oForm = null)
    {
        $this->oForm = $oForm;
    }

    /**
     * @param mixed $aRow
     * @return Display
     */
    public function setRow($aRow)
    {
        $this->aRow = $aRow;
        return $this;
    }

    /**
     * @param string $sTitleSettings
     * @return Display
     */
    public function setTitleSettings($sTitleSettings)
    {
        $this->sTitleSettings = $sTitleSettings;
        return $this;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->aRow[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (isset($this->oForm[$offset])) {
            $oField = $this->oForm->getField($offset);
            if (method_exists($oField, 'setMValue')) {
                $oField->setMValue($this->aRow);
            } else {
                $oField->setValue($this->aRow[$offset]);
            }
            return $oField->getDisplay();
        } else  {
            return $this->aRow[$offset];
        }
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->aRow[$offset] = $value;
        if (isset($this->oForm[$offset])) {
            $this->oForm[$offset]->setValue($value);
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->oForm[$offset]);
        unset($this->aRow[$offset]);
    }

    public function __toString()
    {
        if (!is_null($this->cToStrCallback)) {
            return call_user_func($this->cToStrCallback, $this);
        }
        return implode(':', $this->aRow);
    }

    /**
     * @param mixed $cToStrCallback
     * @return Display
     */
    public function setToStrCallback($cToStrCallback)
    {
        $this->cToStrCallback = $cToStrCallback;
        return $this;
    }

    protected function parseVars($sStr)
    {
        $sPattern = '/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
        preg_match_all($sPattern, $sStr, $aMatches);
        if (isset($aMatches[1]) && count($aMatches[1])) {
            $sRes = $sStr;
            foreach($aMatches[1] as $sVar) {
                $sRes = str_replace('$' . $sVar, $this->offsetGet($sVar), $sRes);
            }
            return $sRes;
        }
        return get_called_class();
    }
}