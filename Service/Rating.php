<?php
namespace Apps\CM_DigitalDownload\Service;


use Apps\CM_DigitalDownload\Lib\Cache\CMCache;
use Phpfox;

class Rating  extends \Phpfox_Service
{
    protected $_sTable = 'digital_download_rating';
    protected $aRatingCache = [];

    function &getRating($iDDId, $aRawValue = null)
    {
        if (!isset($this->aRatingCache[$iDDId])) {

            $aData = !is_null($aRawValue) ? explode('|', $aRawValue) : null;
            if (empty($aData)) {
                $aRes = CMCache::remember('dd_rating_' . $iDDId, [$this, 'calculateRating']);
            } else {
                $aRes = ['rating' => isset($aData[0]) ? $aData[0] : 0, 'count' => isset($aData[1]) ? $aData[1] : 0];
            }

            $this->aRatingCache[$iDDId] = $aRes;
        }
        return $this->aRatingCache[$iDDId];
    }

    function setRating($iDDId, $iRating, $iUserId)
    {
        $this->database()->delete(Phpfox::getT($this->_sTable), '`dd_id` = ' . (int) $iDDId . ' AND `user_id` = ' . (int) $iUserId);
        $this->database()->insert(Phpfox::getT($this->_sTable), [
            'dd_id' => (int) $iDDId,
            'user_id' => (int) $iUserId,
            'time_stamp' => PHPFOX_TIME,
            'rating' => (int) $iRating,
            'ip_address' => $this->request()->getIp(),
        ]);
        if(isset($this->aRatingCache[$iDDId])) {
            unset($this->aRatingCache[$iDDId]);
        }
        CMCache::remove('dd_rating_' . $iDDId);
        $aRating = $this->calculateRating($iDDId);
        $sRating = implode('|', $aRating);
        return Phpfox::getService('digitaldownload.dd')->updateById($iDDId, ['`rating`' => $sRating]);
    }

    public function calculateRating($iDDId)
    {
        return $this->database()
            ->select('avg(`rating`) as `rating`, count(*) as `count`')
            ->from(Phpfox::getT($this->_sTable))
            ->where('`dd_id` = ' . (int) $iDDId)
            ->get();
    }

}