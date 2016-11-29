<?php


namespace Apps\CM_DigitalDownload\Lib\Form\Field;


class Factory
{
    protected $aTypes = [];

    private function getTypeClassName($sType)
    {
        return isset($this->aTypes[$sType])
            ? $this->aTypes[$sType]
            : '\Apps\CM_DigitalDownload\Lib\Form\Field\Type\\' . ucfirst($sType) . 'Type';
    }

    /**
     * @param string $sTypeName
     * @param string $sTypeClassName
     * @return $this
     */
    public function registerType($sTypeName, $sTypeClassName)
    {
        $this->aTypes[$sTypeName] = $sTypeClassName;
        return $this;
    }

    public function createType($sType, $aData)
    {
        $sTypeClass = $this->getTypeClassName($sType);
        return new $sTypeClass($aData);
    }
}