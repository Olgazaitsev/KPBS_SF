<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
define('ADMIN_MODULE_NAME', 'intervolga.sed');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

global $APPLICATION;
global $USER;

//Custom Status

if ($USER->IsAdmin()
    && \Bitrix\Main\Loader::includeModule('intervolga.sed')
    && \Bitrix\Main\Loader::includeModule('tasks')
) {
    $settings = new \Intervolga\Sed\Admin\ListTransition(array(
        'DETAIL_PAGE_URL' => '/bitrix/admin/intervolga.sed_transition_detail.php',
        'TASK_TYPE_DETAIL_PAGE_URL' => '/bitrix/admin/intervolga.sed_type_detail.php',
        'TASK_STATUS_DETAIL_PAGE_URL' => '/bitrix/admin/intervolga.sed_status_detail.php',
        'DETAIL_PAGE_PARAM' => 'TRANSITION',
        'TASK_TYPE_DETAIL_PAGE_PARAM' => 'TYPE',
        'TASK_STATUS_DETAIL_PAGE_PARAM' => 'STATUS'
    ));

    $APPLICATION->IncludeComponent(
        "intervolga.sed:settings.list.v2",
        ".default",
        array(
            'RESULT' => $settings->getResult(),
            'JS_DATA' => array(
                'DETAIL_PAGE_URL' => $settings->getParam('DETAIL_PAGE_URL'),
                'DETAIL_PAGE_PARAM' => $settings->getParam('DETAIL_PAGE_PARAM'),
                'DISABLE_DELETE_ACTION' => 'Y'
            ),
            'CACHE_TYPE' => 'N'
        )
    );
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");