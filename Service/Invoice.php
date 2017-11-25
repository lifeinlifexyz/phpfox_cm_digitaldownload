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
        $aInvoice = $this->database()->select('di.*, d.user_id as dd_user_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT($this->_sTable), 'di')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = di.user_id')
            ->join(Phpfox::getT('digital_download'), 'd', 'd.id = di.dd_id')
            ->where('di.invoice_id = ' . (int)$iId)
            ->get();

        return (isset($aInvoice['invoice_id']) ? $aInvoice : false);
    }

    public function getInvoices($aCond, $bGroupUser = false)
    {
        if ($bGroupUser) {
            $this->database()->group('di.user_id');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT($this->_sTable), 'di')
            ->where($aCond)
            ->execute('getSlaveField');

        if ($bGroupUser) {
            $this->database()->group('di.user_id');
        }

        $aRows = $this->database()->select('di.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT($this->_sTable), 'di')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = di.user_id')
            ->where($aCond)
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            switch ($aRow['status']) {
                case 'completed':
                    $aRows[$iKey]['status_phrase'] = _p('Paid');
                    break;
                case 'cancel':
                    $aRows[$iKey]['status_phrase'] = _p('Cancelled');
                    break;
                case 'pending':
                    $aRows[$iKey]['status_phrase'] = _p('Pending');
                    break;
                default:
                    $aRows[$iKey]['status_phrase'] = _p('Pending');
                    break;
            }
        }

        return array($iCnt, $aRows);
    }

    public function delete($iInvoice){
        $aInvoice = $this->get($iInvoice);

        if (isset($aInvoice['invoice_id'])){
            return $this->database()->delete(Phpfox::getT($this->_sTable), '`invoice_id` = ' . $aInvoice['invoice_id']);
        }
        return false;
    }

}