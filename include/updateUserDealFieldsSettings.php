<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/include/utility.php");

$res = Utility::UpdateDealFieldSettings();

echo json_encode($res);
//Bitrix\Main\Diag\Debug::writeToFile('call update user deal fields settings', "updateUserDealFieldsSettings", "/debug.txt");


