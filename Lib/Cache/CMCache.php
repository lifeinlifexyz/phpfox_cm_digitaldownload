<?php

namespace Apps\CM_DigitalDownload\Lib\Cache;

class CMCache
{
    static public function remember($sKey, $cCallback, $iMinute = 0, $sGroup = null)
    {
       $mData = cache()->get($sKey, $iMinute);
       if (empty($mData)) {
           $mData = call_user_func($cCallback, $sKey);
           cache()->set($sKey, $mData);
           if (!is_null($sGroup)) {
               $sGroup = str_replace(['/', '_'], '', $sGroup);
               $aGroup =  cache()->get('cm_dd_cache_tag_group_' . $sGroup);
               $aGroup = empty($aGroup) ? [] : $aGroup;
               $aGroup[] = $sKey;
               cache()->set('cm_dd_cache_tag_group_' . $sGroup, $aGroup);
           }
       }
       return $mData;
    }

    static public function removeByGroup($sGroup)
    {
        $sGroup = str_replace(['/', '_'], '', $sGroup);
        $aKeys =  cache()->get('cm_dd_cache_tag_group_' . $sGroup);
        if (is_array($aKeys)) {
            foreach($aKeys as $sKey) {
                cache()->del($sKey);
            }
        }
    }

    static function __callStatic($name, $arguments)
    {
        $oCache = cache();
        return call_user_func_array([$oCache, $name], $arguments);
    }
}