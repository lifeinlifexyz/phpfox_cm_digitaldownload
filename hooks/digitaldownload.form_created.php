<?php
/**
 * @var $oForm \Apps\CM_DigitalDownload\Lib\Form\Form
 */
$oForm->getFactory()->registerType('dd', 'Apps\CM_DigitalDownload\Lib\CustomFieldType\DigitalDownload');
$oForm->getFactory()->registerType('dd_price', 'Apps\CM_DigitalDownload\Lib\CustomFieldType\DDPrice');
$oForm->getFactory()->registerType('category', 'Apps\CM_DigitalDownload\Lib\CustomFieldType\Category');