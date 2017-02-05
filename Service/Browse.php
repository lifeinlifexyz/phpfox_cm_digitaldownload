<?php
namespace Apps\CM_DigitalDownload\Service;

use Apps\CM_DigitalDownload\Lib\Collection\Collection;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\FormlyTrait;
use Apps\CM_DigitalDownload\Lib\Form\DataBinding\IFormly;
use Core\Event;
use Phpfox;
use Phpfox_Plugin;

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

    public function similar($sTitle)
    {
        $sQuery = $this->database()->searchKeywords(Phpfox::getService('digitaldownload.field')->getFilterableFieldsName(), $sTitle);
        $this->_aConditions[] = ' AND (' . $sQuery . ')';
        return $this;
    }

    public function get($bOrderWithSponsored = true)
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.before_browse_get')) ? eval($sPlugin) : false);

        $this->_iCnt = $this->database()->select('count(*)')->from(\Phpfox::getT($this->_sTable), 'd')->where($this->_aConditions)->count();

        if ($bOrderWithSponsored) {
            $this->_sSort = '`d`.`sponsored` DESC' . (!empty($this->_sSort) ? ', ' . $this->_sSort : '');
        }
        $aDD = $this->database()
            ->select('`d`.*, u.*')
            ->from(\Phpfox::getT($this->_sTable), 'd')
            ->leftJoin(\Phpfox::getT('user'), 'u', 'u.user_id= d.user_id')
            ->where($this->_aConditions)
            ->order($this->_sSort)
            ->limit($this->_iPage, $this->_iLimit, $this->_iCnt)
            ->all();

        return [$this->_iCnt, $aDD];
    }

    public function getCollection($bOrderWithSponsored = true)
    {
        list(, $aRows) = $this->get($bOrderWithSponsored);
        $oDisplay = new Display(\Phpfox::getService('digitaldownload.dd'));
        $oDisplay->setDDFieldNames(\Phpfox::getService('digitaldownload.field')->getFieldsByType('dd'));
        $oCollection = new Collection($aRows, $oDisplay);
        return $oCollection;
    }

    public function count()
    {
        return $this->_iCnt;
    }
}