<?php

$sLastViewed = Phpfox::getCookie('cm_dd_last_viewed');
$aLastViewed = !empty($sLastViewed) ? json_decode($sLastViewed, true) : [];
$aLastViewed[$iDDId] = $iDDId;
Phpfox::setCookie('cm_dd_last_viewed', json_encode($aLastViewed));