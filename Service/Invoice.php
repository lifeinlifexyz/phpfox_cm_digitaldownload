<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;
use Core\Event;
use Phpfox;
use Phpfox_File;
use Phpfox_Request;

class Invoice extends \Phpfox_Service
{
    protected $_sTable = 'digital_download_invoice';

    public function add($iDDId, $sCurrencyId, $sCost, $sType = 'options', $aData = [])
    {
        $invoice = $this->database()->insert(Phpfox::getT($this->_sTable), [
            'dd_id' => $iDDId,
            'currency_id' => $sCurrencyId,
            'price' => $sCost,
            'time_stamp' => PHPFOX_TIME,
            'user_id' => Phpfox::getUserId(),
            'data' => json_encode($aData),
            'type' => $sType,
        ]);

        return $invoice;
    }

    public function get($iId)
    {
        $aInvoice = $this->database()->select('*')
            ->from(Phpfox::getT('digital_download_invoice'), 'di')
            ->where('di.invoice_id = ' . (int)$iId)
            ->get();

        return (isset($aInvoice['invoice_id']) ? $aInvoice : false);
    }

}