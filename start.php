<?php

//todo:: филд тип поля не правильно показвает иархию

\Phpfox_Module::instance()
    ->addServiceNames([
        'digitaldownload.category' => '\Apps\CM_DigitalDownload\Service\Category',
        'digitaldownload.field' => '\Apps\CM_DigitalDownload\Service\Field',
    ])
    ->addComponentNames('controller', [
        'digitaldownload.admincp.add-category' => '\Apps\CM_DigitalDownload\Controller\Admin\AddCategoryController',
        'digitaldownload.admincp.categories' => '\Apps\CM_DigitalDownload\Controller\Admin\CategoriesController',
        'digitaldownload.admincp.fields' => '\Apps\CM_DigitalDownload\Controller\Admin\FieldsController',
        'digitaldownload.admincp.add-field' => '\Apps\CM_DigitalDownload\Controller\Admin\AddFieldController',
        'digitaldownload.admincp.save-field' => '\Apps\CM_DigitalDownload\Controller\Admin\SaveFieldController',
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
    route('fields', 'digitaldownload.admincp.fields');
    route('fields/add', 'digitaldownload.admincp.add-field');

    /**
     * set status
     */
    route('categories/status', function(){
        \Phpfox::isAdmin(true);
        $iStatus = request()->getInt('status');
        $iIds = request()->get('ids');
        Phpfox::getService('digitaldownload.category')->setStatus($iStatus, $iIds);
    });

    /**
     * set order
     */
    route('categories/order', function(){
        \Phpfox::isAdmin(true);
        $aOrder = request()->get('order');
        Phpfox::getService('digitaldownload.category')->order($aOrder);
    });

    /**
     * delete category
     */
    route('categories/delete', function(){
        \Phpfox::isAdmin(true);
        Phpfox::getService('digitaldownload.category')->delete(request()->getInt('id'));
    });

});
group('/digitaldownload/', function (){
    route('admincp/add-category', 'digitaldownload.admincp.add-category');
    route('admincp/save-field', 'digitaldownload.admincp.save-field');
});