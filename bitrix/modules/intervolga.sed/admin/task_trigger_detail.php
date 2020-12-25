<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
define('ADMIN_MODULE_NAME', 'intervolga.sed');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

global $APPLICATION;
global $USER;

if ($USER->IsAdmin() && \Bitrix\Main\Loader::includeModule('intervolga.sed') && \Bitrix\Main\Loader::includeModule('tasks')) {
    $settings = new \Intervolga\Sed\Admin\DetailTaskTrigger(array(
        'LIST_PAGE_URL' => '/bitrix/admin/intervolga.sed_task_trigger_list.php',
        'UNIQUE_ENTITY_CODE' => 'TASK_TRIGGER'
    ));

    $APPLICATION->IncludeComponent(
        "intervolga.sed:settings.detail.trigger.task",
        ".default",
        array(
            'RESULT' => $settings->getResult(),
            'JS_DATA' => array(
                'ACTIONS' => $settings->getActions(),
                'SELECTED_ACTIONS' => $settings->getSelectedActions(),
//                'PARAM_OPTIONS' => $settings->getActionParamOptions(),
                'PARAM_INPUT_PREFIX' => \Intervolga\Sed\Admin\DetailTaskTrigger::PARAM_INPUT_PREFIX,
                'ACTION_INPUT_PREFIX' => \Intervolga\Sed\Admin\DetailTaskTrigger::ACTION_INPUT_PREFIX
            ),
            'CACHE_TYPE' => 'N'
        )
    );
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");