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
    $settings = new \Intervolga\Sed\Admin\CreateTaskType(array(
        'LIST_PAGE_URL' => '/bitrix/admin/intervolga.sed_ttype_list.php',
        'DETAIL_PAGE_URL' => '/bitrix/admin/intervolga.sed_status_list.php',
    ));

    $APPLICATION->IncludeComponent(
        "intervolga.sed:settings.detail.v2",
        ".default",
        array(
            'RESULT' => $settings->getResult(),
            'CACHE_TYPE' => 'N'
        )
    );
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");