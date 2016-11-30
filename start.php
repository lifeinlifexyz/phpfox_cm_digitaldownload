<?php

//todo:: филд тип поля не правильно показвает иархию
//todo:: активация филда. чекбок ведет себе не корректно

\Phpfox_Module::instance()
    ->addServiceNames([
        'digitaldownload.category' => '\Apps\CM_DigitalDownload\Service\Category',
        'digitaldownload.field' => '\Apps\CM_DigitalDownload\Service\Field',
        'digitaldownload.categoryField' => '\Apps\CM_DigitalDownload\Service\CategoryField',
        'digitaldownload.dd' => '\Apps\CM_DigitalDownload\Service\DigitalDownload',
        'digitaldownload.browse' => '\Apps\CM_DigitalDownload\Service\Browse',
        'digitaldownload.images' => '\Apps\CM_DigitalDownload\Service\Images',
    ])
    ->addComponentNames('controller', [
        'digitaldownload.admincp.add-category' => '\Apps\CM_DigitalDownload\Controller\Admin\AddCategoryController',
        'digitaldownload.admincp.category-fields' => '\Apps\CM_DigitalDownload\Controller\Admin\CategoryFieldsController',
        'digitaldownload.admincp.categories' => '\Apps\CM_DigitalDownload\Controller\Admin\CategoriesController',
        'digitaldownload.admincp.fields' => '\Apps\CM_DigitalDownload\Controller\Admin\FieldsController',
        'digitaldownload.admincp.add-field' => '\Apps\CM_DigitalDownload\Controller\Admin\AddFieldController',
        'digitaldownload.admincp.save-field' => '\Apps\CM_DigitalDownload\Controller\Admin\SaveFieldController',

        'digitaldownload.category' => '\Apps\CM_DigitalDownload\Controller\CategoryController',
    ])
    ->addAliasNames('digitaldownload', 'CM_DigitalDownload')
    ->addTemplateDirs([
        'digitaldownload' => PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'views',
        'cm_forms' => PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'views'. PHPFOX_DS . 'form',
        'cm_filter_form' => PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'views'. PHPFOX_DS . 'filter',
    ]);

event('app_settings', function ($settings){
    if (isset($settings['cm_dd_enabled'])) {
        \Phpfox::getService('admincp.module.process')->updateActivity('CM_DigitalDownload', $settings['cm_dd_enabled']);
    }
});

if (setting('cm_dd_enabled')) {

    \Phpfox_Module::instance()->addComponentNames('ajax', [
        'digitaldownload.ajax'        => '\Apps\CM_DigitalDownload\Ajax\Ajax',
    ])->addComponentNames('controller', [
        'digitaldownload.index' => '\Apps\CM_DigitalDownload\Controller\IndexController',
        'digitaldownload.add' => '\Apps\CM_DigitalDownload\Controller\AddController',
    ])->addComponentNames('block', [
        'digitaldownload.filter'    => '\Apps\CM_DigitalDownload\Block\Filter',
        'digitaldownload.entry'    => '\Apps\CM_DigitalDownload\Block\Entry',
        'digitaldownload.managephotos'    => '\Apps\CM_DigitalDownload\Block\ManagePhotos',
    ]);
}

group('/admincp/digitaldownload/', function(){

    route('categories', 'digitaldownload.admincp.categories');
    route('category/fields', 'digitaldownload.admincp.category-fields');
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
    route('admincp/delete-field', 'digitaldownload.admincp.fields');

    route('admincp/fields/order', function (){
        auth()->isAdmin(true);

        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[ $id ] = $key + 1;
        }
        \Phpfox::getService('core.process')->updateOrdering([
                'table'  => 'digital_download_fields',
                'key' => 'field_id',
                'values' => $values,
            ]
        );

        //todo::remove fields cache

        return true;
    });

    if (setting('cm_dd_enabled')) {
        route('/', 'digitaldownload.index');
        route('my', 'digitaldownload.index');
        route('add', 'digitaldownload.add');
        route('add/:id', 'digitaldownload.add')->where([':id' => '([0-9]+)']);
        route('add/:id/*', 'digitaldownload.add')->where([':id' => '([0-9]+)']);
    }
});