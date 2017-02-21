<?php

namespace Apps\CM_DigitalDownload\Lib\Cache;

class CMCache
{
    static public function remember($sKey, $cCallback, $iMinute = 0)
    {
       $mData = cache()->get($sKey, $iMinute);
       if (empty($mData)) {
           $mData = call_user_func($cCallback, $sKey);
           cache()->set($sKey, $mData);
       }
       return $mData;
    }

    static function remove($sKey = null)
    {
        cache()->del($sKey);
    }

    static function __callStatic($name, $arguments)
    {
        $oCache = cache();
        return call_user_func_array([$oCache, $name], $arguments);
    }
}