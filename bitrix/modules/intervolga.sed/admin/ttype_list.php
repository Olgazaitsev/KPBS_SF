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
    $settings = new \Intervolga\Sed\Admin\ListTaskType(array(
        'DETAIL_PAGE_URL' => '/bitrix/admin/intervolga.sed_c_status_list.php',
        'CREATE_PROCESS_TTYPE_PAGE_URL' => '/bitrix/admin/intervolga.sed_ttype_create.php',
        'DETAIL_PAGE_PARAM' => 'TYPE'
    ));

    $APPLICATION->IncludeComponent(
        "intervolga.sed:settings.list.v2",
        ".default",
        array(
            'RESULT' => $settings->getResult(),
            'JS_DATA' => array(
                'DETAIL_PAGE_URL' => $settings->getParam('DETAIL_PAGE_URL'),
                'DETAIL_PAGE_PARAM' => $settings->getParam('DETAIL_PAGE_PARAM'),
                'AJAX_URL' => '/bitrix/tools/intervolga.sed/ajax.php',
                'DELETE_ACTION_NAME' => 'DeleteTaskType'
            ),
            'CACHE_TYPE' => 'N'
        )
    );
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");