<?php
define('STOP_STATISTICS',    true);
define('NO_AGENT_CHECK',     true);
define('DisableEventsCheck', true);
define('PUBLIC_AJAX_MODE', true);

require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

if(!\Bitrix\Main\Loader::includeModule('intervolga.sed') || !\Bitrix\Main\Loader::includeModule('tasks')) {
    echo json_encode(array(
        'resultData' => null,
        'errorInfo' => array(
            'type' => 'module',
            'description' => 'module \'intervolga.sed\' is not included'
        )
    ));
}
else {
    \Intervolga\Sed\Tools\AjaxUtils::processRequest();
}