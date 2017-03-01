<?php

Phpfox_File::instance()->delete_directory(Phpfox::getParam('core.dir_pic') . 'digitaldownload/');

$aFields = Phpfox::getService('digitaldownload.field')->getFieldsByType('dd');
foreach($aFields as $sField) {
    Phpfox_File::instance()->delete_directory(PHPFOX_DIR_FILE . $sField . '_dd_files');
}

$aTables = [
    Phpfox::getT('digital_download_category'),
    Phpfox::getT('digital_download_fields'),
    Phpfox::getT('digital_download_category_fields'),
    Phpfox::getT('digital_download'),
    Phpfox::getT('digital_download_plans'),
    Phpfox::getT('digital_download_invoice'),
    Phpfox::getT('digital_download_dd_plan'),
    Phpfox::getT('digital_download_download'),
    Phpfox::getT('digital_download_invite'),
    Phpfox::getT('digital_download_rating'),
];
db()->dropTables($aTables);