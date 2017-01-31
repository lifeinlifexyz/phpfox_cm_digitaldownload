<?php

namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Apps\CM_DigitalDownload\Lib\Tree\Tree;
use Core\Event;
use Phpfox;
use Phpfox_File;
use Phpfox_Request;

class Download extends \Phpfox_Service
{
    protected $_sTable = 'digital_download_download';

    public function add($aVals)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        return $this->database()->insert(Phpfox::getT($this->_sTable), [
            '`dd_id`' => (int) $aVals['dd_id'],
            '`user_id`' => (int) $aVals['user_id'],
            '`field`' => $oParseInput->clean($aVals['field'], 255),
            '`limit`' => (int) $aVals['limit'],
        ]);
    }


    public function canDownload($iUserId, $iDDId, $sField)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        $aDownload = $this->database()
            ->select('*')
            ->from(Phpfox::getT($this->_sTable))
            ->where([
                '`user_id` = ' . (int) $iUserId,
                'AND `dd_id` = ' . (int) $iDDId,
                'AND `field` = \'' . $oParseInput->clean($sField, 255) . '\'',
                'AND `limit` > 0'
            ])->get();

        return !empty($aDownload);
    }

    public function decrementLimit($iUserId, $iDDId, $sField)
    {
        $oParseInput = Phpfox::getLib('parse.input');

        $iCount = $this->database()
            ->select('`limit`')
            ->from(Phpfox::getT($this->_sTable))
            ->where([
                '`user_id` = ' . (int) $iUserId,
                'AND `dd_id` = ' . (int) $iDDId,
                'AND `field` = \'' . $oParseInput->clean($sField, 255) . '\'',
            ])->execute('getSlaveField');

        if ($iCount <= 0) {
            \Phpfox_Url::instance()->send('digitaldownload.' . $iDDId, [], _p('You have reached download limit'));
        }

        $this->database()
            ->update(
                Phpfox::getT($this->_sTable),
                ['`limit`' => ($iCount - 1)],
                '`user_id` = ' . (int) $iUserId . ' AND `dd_id` = ' . (int) $iDDId . ' AND `field` = \'' . $oParseInput->clean($sField, 255) . '\'');

        return $this;
    }

    public function download($iDDId, $sField)
    {

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