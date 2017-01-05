<?php
namespace Apps\CM_DigitalDownload\Lib\Form\Field;


use Apps\CM_DigitalDownload\Lib\Form\Exception\RequiredArgumentException;
use Apps\CM_DigitalDownload\Lib\Form\Field\IType;
use Apps\CM_DigitalDownload\Lib\Form\Validator\IValidator;
use Core\View;

abstract class AbstractType implements IType, \JsonSerializable
{
    protected $aRules = [];
    protected $aInfo = [
        'template' => '@CM_DigitalDownload/form/fields/string.html',
    ];

    protected $aErrors = [];

    protected $aColumnDefinitions = [
          [
              'type' => 'VARCHAR(250)',
              'null' => 'NULL',
          ]
    ];
    protected $bHasError = false;

    /**
     * @var View
     */
    protected $oView;
    /**
     * @var IValidator
     */
    protected $oValidator = null;

    /**
     * AbstractType constructor.
     * <code>
     *   $oText = new TextType(
     * [
     *      'name' => 'username',
     *      'Caption' => 'Your Name',
     *      'required' => true,
     * ]
     * );
     * </code>
     * @param array $aData - data of type
     */
    public function __construct(array $aData)
    {
        if (!isset($aData['name'])) {
            throw  new RequiredArgumentException('Required element "name" in argument aData');
        }

        if (!isset($aData['title'])) {
            throw  new RequiredArgumentException('Required element "title" in argument aData');
        }

        if (isset($aData['rules'])) {
            $this->aRules = [
                $aData['name'] => is_array($aData['rules']) ? $aData['rules'] : explode('|', $aData['rules']),
            ];
        }

        $this->aInfo = array_merge($this->aInfo, $aData);
    }

    public function setHasError($bHas)
    {
        $this->bHasError = $bHas;
    }

    public function hasError()
    {
        return $this->bHasError;
    }

    public function setValue($mValue)
    {
        $this->aInfo['value'] = $mValue;
        return $this;
    }

    public function setCondition(\Phpfox_Search &$oSearch, $aSearch)
    {
        $sKey = $this->aInfo['column'];
        $sTAlias = $this->aInfo['table_alias'];
        if (($sValue = $oSearch->get($sKey)) || (isset($aSearch[$sKey]) && $sValue = $aSearch[$sKey])) {
            $oSearch->setCondition('AND `' . $sTAlias . '`.`' . $sKey . '` = ' . $sValue);
        }
    }

    /**
     * @return array
     */
    protected function getVars()
    {
        $this->aInfo['required'] = isset($this->aInfo['rules']) && (strpos($this->aInfo['rules'], 'required') !== false);
        return $this->aInfo;
    }

    public function getDisplay()
    {
       return $this->getValue();
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->aInfo['value']);
    }

    public function render($sTemplate = null)
    {
        $sTpl = is_null($sTemplate) ? $this->aInfo['template'] : $sTemplate;
        $aVars = $this->getVars();
        $aVars['hasError'] = $this->bHasError;
        $aVars['errors'] = $this->bHasError ? $this->getErrors(): null;
        return $this->oView->view($sTpl, $aVars);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return (isset($this->aInfo['value']) ? $this->aInfo['value'] : null);
    }

    public function isValid()
    {
        $aInfo = $this->aInfo;

        if (!isset($this->aRules[$aInfo['name']])) {
            return true;
        }

        $bResult = true;

        if (!$this->isEmpty() && (count($this->aRules) > 0)) {

            $oValidator = &$this->oValidator;
            $oValidator->setRules($this->aRules)
                ->setData([$aInfo['name'] => $this->getValue()]);

            if (isset($aInfo['errorMessages'])) {
                $oValidator->setErrorMessages($aInfo['errorMessages']);
            }

            if (!$oValidator->isValid()) {
                $bResult = false;
                $this->aErrors = $oValidator->getErrors()[$aInfo['name']];
            }

        } elseif(in_array('required', $this->aRules[$aInfo['name']]) && $this->isEmpty()) {
            $this->aErrors[] = isset($aInfo['errorMessages'][$aInfo['name'] . '.required'])
                ? $aInfo['errorMessages'][$aInfo['name'] . '.required']
                : 'The field "' . $aInfo['name'] .'" is required';
            $bResult = false;
        }

        $this->bHasError = !$bResult;

        return $bResult;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->oView;
    }

    /**
     * @param View $oView
     * @return  $this
     */
    public function setView(View $oView)
    {
        $this->oView = $oView;
        return $this;
    }

    public function __toString()
    {
        try {
            return (string)$this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->aInfo;
    }

    /**
     * @return array
     */
    public function getColumnDefinitions() {
        return $this->aColumnDefinitions;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->getInfo();
    }

    /**
     * @return array | null
     */
    public function getRules()
    {
        return empty($this->aRules) ? null : $this->aRules;
    }

    /**
     * @param array $aRules
     */
    public function setRules($aRules)
    {
        $this->aRules = $aRules;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->oValidator;
    }


    /**
     * @param $oValidator IValidator
     * @return $this
     */
    public function setValidator($oValidator)
    {
        $this->oValidator = $oValidator;
        return $this;
    }

    /**
     * @param $sTpl
     * @return $this
     */
    public function setTemplate($sTpl)
    {
        $this->aInfo['template'] = $sTpl;
    }
}