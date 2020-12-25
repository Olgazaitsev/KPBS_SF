<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/* --- iNCLUDE CUSTOM STATUS ---*/

// подключить необходимый js
\CJSCore::RegisterExt(
        'custom_task_statuses',
        array(
                'js' => '/bitrix/js/intervolga.sed/custom_task_statuses.js',
                'css' => '/bitrix/css/intervolga.sed/custom_task_statuses.css'
        )
);
\CJSCore::RegisterExt(
        'deal_document_generator_buttons_extension',
        array(
                'js' => '/bitrix/js/intervolga.sed/deal_document_generator_buttons_extension.js',
        )
);
\CJSCore::Init('custom_task_statuses');

// подписываемся на события модуля intenvolga.injections
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnAfterComponentTemplatePage',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onAfterComponentTemplatePage'
    )
);


\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'tasks',
    'OnBeforeTaskNotificationSend',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskNotificationSend'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'tasks',
    'OnBeforeTaskAdd',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskAdd'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'tasks',
    'OnBeforeTaskUpdate',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskUpdate'
    ),
    false,
    200
);

/* --- END INCLUDE CUSTOM STATUS ---*/


\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'tasks',
    'OnTaskUpdate',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onTaskUpdate'
    ),
    false,
    400
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'tasks',
    'OnBeforeTaskDelete',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskDelete'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnTaskTransitionsCreated',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onTaskTransitionsCreated'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnAfterTaskStatusAdd',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onAfterTaskStatusAdd'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnAfterTaskStatusRemove',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onAfterTaskStatusRemove'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnBeforeTaskStatusRemove',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskStatusRemove'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnBeforeTaskStatusUpdate',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskStatusUpdate'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'intervolga.sed',
    'OnBeforeTaskTypeRemove',
    array(
        '\Intervolga\Sed\Tools\Handler',
        'onBeforeTaskTypeRemove'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
        'intervolga.sed',
        'OnBeforeTaskTypeUpdate',
        array(
                '\Intervolga\Sed\Tools\Handler',
                'onBeforeTaskTypeUpdate'
        )
);
?>