<?php
/*
Группы:
13 - KBPS: Маркетинг и продажи
// 25 - KPBS- Development
// 28 - KPBS- Engineer\Legal
// 20 - KPBS-Marketing
// 19 - KPBS- Sales
*/

/*$GLOBALS["FIELDS_ACCESS_DENIED"] = array(
// 13 - KPBS: Маркетинг и продажи
    ["GroupId" => 13, "UserField" => "UF_CRM_1576676729064"], // Чистая прибыль
    ["GroupId" => 13, "UserField" => "UF_CRM_1577693021954"], // Плановая маржа
    ["GroupId" => 13, "UserField" => "UF_CRM_1579077455200"], // КВ
    ["GroupId" => 13, "UserField" => "UF_CRM_1581250888107"], // Сумма сделки
    ["GroupId" => 13, "UserField" => "UF_CRM_1581251094746"], // Цена продажи
    ["GroupId" => 13, "UserField" => "UF_CRM_1581251108039"],  // Цена покупки
    ["GroupId" => 13, "UserField" => "FORMATTED_OPPORTUNITY"],
    ["GroupId" => 13, "UserField" => "OPPORTUNITY_WITH_CURRENCY"],
    ["GroupId" => 13, "UserField" => "OPPORTUNITY"],
// 25 - KPBS- Development
    ["GroupId" => 25, "UserField" => "UF_CRM_1576676729064"], // Чистая прибыль
    ["GroupId" => 25, "UserField" => "UF_CRM_1577693021954"], // Плановая маржа
    ["GroupId" => 25, "UserField" => "UF_CRM_1579077455200"], // КВ
    ["GroupId" => 25, "UserField" => "UF_CRM_1581250888107"], // Сумма сделки
    ["GroupId" => 25, "UserField" => "UF_CRM_1581251094746"], // Цена продажи
    ["GroupId" => 25, "UserField" => "UF_CRM_1581251108039"],  // Цена покупки
    ["GroupId" => 25, "UserField" => "FORMATTED_OPPORTUNITY"],
    ["GroupId" => 25, "UserField" => "OPPORTUNITY_WITH_CURRENCY"],
    ["GroupId" => 25, "UserField" => "OPPORTUNITY"],
// 28 - KPBS- Engineer\Legal
    ["GroupId" => 28, "UserField" => "UF_CRM_1576676729064"], // Чистая прибыль
    ["GroupId" => 28, "UserField" => "UF_CRM_1577693021954"], // Плановая маржа
    ["GroupId" => 28, "UserField" => "UF_CRM_1579077455200"], // КВ
    ["GroupId" => 28, "UserField" => "UF_CRM_1581250888107"], // Сумма сделки
    ["GroupId" => 28, "UserField" => "UF_CRM_1581251094746"], // Цена продажи
    ["GroupId" => 28, "UserField" => "UF_CRM_1581251108039"],  // Цена покупки
    ["GroupId" => 28, "UserField" => "FORMATTED_OPPORTUNITY"],
    ["GroupId" => 28, "UserField" => "OPPORTUNITY_WITH_CURRENCY"],
    ["GroupId" => 28, "UserField" => "OPPORTUNITY"],
// 20 - KPBS-Marketing
    ["GroupId" => 20, "UserField" => "UF_CRM_1576676729064"], // Чистая прибыль
    ["GroupId" => 20, "UserField" => "UF_CRM_1577693021954"], // Плановая маржа
    ["GroupId" => 20, "UserField" => "UF_CRM_1579077455200"], // КВ
    ["GroupId" => 20, "UserField" => "UF_CRM_1581250888107"], // Сумма сделки
    ["GroupId" => 20, "UserField" => "UF_CRM_1581251094746"], // Цена продажи
    ["GroupId" => 20, "UserField" => "UF_CRM_1581251108039"],  // Цена покупки
    ["GroupId" => 20, "UserField" => "FORMATTED_OPPORTUNITY"],
    ["GroupId" => 20, "UserField" => "OPPORTUNITY_WITH_CURRENCY"],
    ["GroupId" => 20, "UserField" => "OPPORTUNITY"],
// 19 - KPBS- Sales
    ["GroupId" => 19, "UserField" => "UF_CRM_1576676729064"], // Чистая прибыль
    ["GroupId" => 19, "UserField" => "UF_CRM_1577693021954"], // Плановая маржа
    ["GroupId" => 19, "UserField" => "UF_CRM_1579077455200"], // КВ
    ["GroupId" => 19, "UserField" => "UF_CRM_1581250888107"], // Сумма сделки
    ["GroupId" => 19, "UserField" => "UF_CRM_1581251094746"], // Цена продажи
    ["GroupId" => 19, "UserField" => "UF_CRM_1581251108039"],  // Цена покупки
    ["GroupId" => 19, "UserField" => "FORMATTED_OPPORTUNITY"],
    ["GroupId" => 19, "UserField" => "OPPORTUNITY_WITH_CURRENCY"],
    ["GroupId" => 19, "UserField" => "OPPORTUNITY"]
    //    ["GroupId" => 10, "UserField" => "UF_CRM_1584359379931"]
);*/
$res = \Bitrix\Main\GroupTable::getList(
    array(
        // выбераем название, идентификатор, символьный код, сортировку
        'select' => array('NAME', 'ID', 'STRING_ID', 'C_SORT'),
        // все группы, кроме основной группы администраторов
        'filter' => array('!ID' => '1')
    )
);

$targetgroup = array();
$pattern = '/EMPTY/';

while ($arResGroup = $res->Fetch()) {
    $group = COption::GetOptionString('kpbs.custom', 'group_'.$arResGroup['ID']);
    //print_r("Группа".$arResGroup['ID']);
    //echo "<br/>";
    //print_r($group);
    if($group) {
        if(!preg_match($pattern, $group)) {
            $ufieldsarr = explode(",",$group);
            //print_r($ufieldsarr);
            foreach($ufieldsarr as $ufield) {
                $ufarritem = ["GroupId" => $arResGroup['ID'], "UserField" => $ufield];
                array_push($targetgroup, $ufarritem);
            }
        }
    }
    //print_r($matches);
    //print_r($mathes);

}

$GLOBALS["FIELDS_ACCESS_DENIED"] = $targetgroup;
