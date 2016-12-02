<?php
namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Collection\Collection;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FilterTraid;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Core\Theme;

class Browse  extends \Phpfox_Service
{

    private $_aConditions = array();
    private $_sSort = 'c.time_stamp DESC';
    private $_iPage = 0;
    private $_iLimit = 9;
    private $_iCnt = 0;
    protected $_sTable = 'digital_download';

    public function conditions($aCond)
    {
        $this->_aConditions = $aCond;
        return $this;
    }

    public function sort($sSort)
    {
        $this->_sSort = $sSort;
        return $this;
    }

    public function page($iPage)
    {
        $this->_iPage = $iPage;
        return $this;
    }

    public function limit($iLimit)
    {
        $this->_iLimit = $iLimit;
        return $this;
    }

    public function get()
    {
        $this->_iCnt = $this->database()->select('count(*)')->from(\Phpfox::getT($this->_sTable), 'd')->where($this->_aConditions)->count();

        $aDD = $this->database()
            ->select('`d`.*')
            ->from(\Phpfox::getT($this->_sTable), 'd')
            ->where($this->_aConditions)
            ->limit($this->_iPage, $this->_iLimit, $this->_iCnt)
            ->all();

        return [$this->_iCnt, $aDD];
    }

    public function getCollection()
    {
        list(, $aRows) = $this->get();
        $oDisplay = new Display(\Phpfox::getService('digitaldownload.dd'));
        $oCollection = new Collection($aRows, $oDisplay);
        return $oCollection;
    }

    public function count()
    {
        return $this->_iCnt;
    }
}