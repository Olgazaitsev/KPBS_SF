<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CJSCore::Init(array("jquery"));
$APPLICATION->SetTitle("Отчет по квартальным КПЭ менеджеров");
$APPLICATION->IncludeComponent(
    'kpbs.custom:kpi.calculation',
    '',
    array(
    ),
    array('HIDE_ICONS' => 'Y',)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
