<?php
define('DD_ASSET_PATH', '//' . \Phpfox::getParam('core.host')
    . str_replace('/index.php', '', \Phpfox::getParam('core.folder'))
    . 'PF.Site/Apps/CM_DigitalDownload/assets/');
//todo:: активация филда. чекбок ведет себе не корректно
//todo:: после пагинации ползунок цен не работает
//todo:: send message complete invoice
//todo:: check user id and activated in view page

\Phpfox_Module::instance()
    ->addServiceNames([
        'digitaldownload.category' => '\Apps\CM_DigitalDownload\Service\Category',
        'digitaldownload.field' => '\Apps\CM_DigitalDownload\Service\Field',
        'digitaldownload.plan' => '\Apps\CM_DigitalDownload\Service\Plan',
        'digitaldownload.categoryField' => '\Apps\CM_DigitalDownload\Service\CategoryField',
        'digitaldownload.dd' => '\Apps\CM_DigitalDownload\Service\DigitalDownload',
        'digitaldownload.browse' => '\Apps\CM_DigitalDownload\Service\Browse',
        'digitaldownload.images' => '\Apps\CM_DigitalDownload\Service\Images',
        'digitaldownload.invoice' => '\Apps\CM_DigitalDownload\Service\Invoice',
        'digitaldownload.callback' => '\Apps\CM_DigitalDownload\Service\Callback',
    ])
    ->addComponentNames('controller', [
        'digitaldownload.admincp.add-category' => '\Apps\CM_DigitalDownload\Controller\Admin\AddCategoryController',
        'digitaldownload.admincp.category-fields' => '\Apps\CM_DigitalDownload\Controller\Admin\CategoryFieldsController',
        'digitaldownload.admincp.categories' => '\Apps\CM_DigitalDownload\Controller\Admin\CategoriesController',
        'digitaldownload.admincp.fields' => '\Apps\CM_DigitalDownload\Controller\Admin\FieldsController',
        'digitaldownload.admincp.add-field' => '\Apps\CM_DigitalDownload\Controller\Admin\AddFieldController',
        'digitaldownload.admincp.save-field' => '\Apps\CM_DigitalDownload\Controller\Admin\SaveFieldController',
        'digitaldownload.admincp.plans' => '\Apps\CM_DigitalDownload\Controller\Admin\PlansController',
        'digitaldownload.admincp.add-plan' => '\Apps\CM_DigitalDownload\Controller\Admin\AddPlanController',
        'digitaldownload.admincp.save-plan' => '\Apps\CM_DigitalDownload\Controller\Admin\SavePlanController',

        'digitaldownload.category' => '\Apps\CM_DigitalDownload\Controller\CategoryController',
        'digitaldownload.plan' => '\Apps\CM_DigitalDownload\Controller\PlanController',
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
        'digitaldownload.apply-options'    => '\Apps\CM_DigitalDownload\Controller\ApplyOptionsController',
        'digitaldownload.purchase'    => '\Apps\CM_DigitalDownload\Controller\PurchaseController',
        'digitaldownload.view'    => '\Apps\CM_DigitalDownload\Controller\ViewController',
    ])->addComponentNames('block', [
        'digitaldownload.filter'    => '\Apps\CM_DigitalDownload\Block\Filter',
        'digitaldownload.entry'    => '\Apps\CM_DigitalDownload\Block\Entry',
        'digitaldownload.info'    => '\Apps\CM_DigitalDownload\Block\Info',
        'digitaldownload.manage-photos'    => '\Apps\CM_DigitalDownload\Block\ManagePhotos',
        'digitaldownload.manage-options'    => '\Apps\CM_DigitalDownload\Block\ManageOptions',
    ]);
}

group('/admincp/digitaldownload/', function(){

    route('categories', 'digitaldownload.admincp.categories');
    route('category/fields', 'digitaldownload.admincp.category-fields');
    route('fields', 'digitaldownload.admincp.fields');
    route('fields/add', 'digitaldownload.admincp.add-field');
    route('plans', 'digitaldownload.admincp.plans');
    route('plan/add', 'digitaldownload.admincp.add-plan');

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
    route('admincp/save-plan', 'digitaldownload.admincp.save-plan');
    route('admincp/delete-plan', 'digitaldownload.admincp.plans');

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
        route(':id', 'digitaldownload.view')->where([':id' => '([0-9]+)']);
        route('purchase', 'digitaldownload.purchase');
    }
});