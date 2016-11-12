<?php
\Phpfox_Module::instance()
    ->addServiceNames([
        'digitaldownload.category' => '\Apps\CM_DigitalDownload\Service\Category',
    ])
    ->addComponentNames('controller', [
        'digitaldownload.admincp.add-category' => '\Apps\CM_DigitalDownload\Controller\Admin\AddCategoryController',
        'digitaldownload.admincp.categories' => '\Apps\CM_DigitalDownload\Controller\Admin\CategoriesController',
    ])
    ->addAliasNames('digitaldownload', 'CM_DigitalDownload')
    ->addTemplateDirs([
        'digitaldownload' => PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'views',
        'cm_forms' => PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'views'. PHPFOX_DS . 'form',
    ]);

event('app_settings', function ($settings){
    if (isset($settings['cm_dd_enabled'])) {
        \Phpfox::getService('admincp.module.process')->updateActivity('CM_DigitalDownload', $settings['cm_dd_enabled']);
    }
});

group('/admincp/digitaldownload/', function(){
    route('categories', 'digitaldownload.admincp.categories');

    route('category/status', function(){
        \Phpfox::isAdmin(true);
        $iStatus = request()->getInt('status');
        $iId = request()->getInt('id');
        Phpfox::getService('digitaldownload.category')->setStatus($iStatus, $iId);
    });

});
group('/digitaldownload/', function (){
    route('admincp/add-category', 'digitaldownload.admincp.add-category');
});