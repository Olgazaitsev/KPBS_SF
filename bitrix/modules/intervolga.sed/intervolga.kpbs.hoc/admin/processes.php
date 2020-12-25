<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");

var_dump(__FILE__);

global $APPLICATION;
$APPLICATION->IncludeComponent(
    "intervolga.sed:settings.processes",
    ".default",
    array(),
    false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
