<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/kpbs/modules/TasksUtility.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/kpbs/modules/CompaniesUtility.php");

Bitrix\Main\Diag\Debug::writeToFile((new DateTime('now'))->format('Y.m.d H:i:s'),"Начало обработки обработки NDA","/checkNda.log");

// Найти компании, заведенные старше 2 недель
$arCompaniesWithoutNDA = CompaniesUtility::GetCompaniesWithoutNDA();
// Ответственному создать задачу на заполнение информации о NDA
if(isset($arCompaniesWithoutNDA) && is_array($arCompaniesWithoutNDA)){
    foreach ($arCompaniesWithoutNDA as $cN) {
        try {
            // Для тестирования ограничиваем пользователя Димой
            // if($cN["RESPONSIBLE_ID"] == 9)
            TasksUtility::CreateTaskToFillNDA($cN);
        }catch (\Throwable $e){
            Bitrix\Main\Diag\Debug::writeToFile($e,"Ошибка при создании задачи для заполнения данных NDA для ".$cN["TITLE"],"/checkNda.log");
        }
    }
}

// Найти компании, у которых скоро кончится NDA
$arCompaniesWithNDAMonthToEnd = CompaniesUtility::GetCompaniesWithNDAMonthToEnd();
// Ответственному создать задачу на продление NDA за месяц до окончания NDA
if(isset($arCompaniesWithNDAMonthToEnd) && is_array($arCompaniesWithNDAMonthToEnd)){
    foreach ($arCompaniesWithNDAMonthToEnd as $cE) {
        try {
            // Для тестирования ограничиваем пользователя Димой
            // if($cE["RESPONSIBLE_ID"] == 9)
            TasksUtility::CreateTaskToProlongateNDA($cE);
        }catch (\Throwable $e){
            Bitrix\Main\Diag\Debug::writeToFile($e,"Ошибка при создании задачи продления NDA для ".$cN["TITLE"],"/checkNda.log");
        }
    }
}

Bitrix\Main\Diag\Debug::writeToFile((new DateTime('now'))->format('Y.m.d H:i:s'),"Конец обработки NDA","/checkNda.log");

