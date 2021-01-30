<?php
$arResult['CONTROLS']['SHOW_ALL'] = 'on';
if (\Bitrix\Main\Loader::includeModule("iblock")) {
    $sections = CIBlockSection::GetList (
        Array("ID" => "ASC"),
        Array("IBLOCK_ID" => 5, "ACTIVE" => "Y"),
        false,
        Array('ID', 'NAME', 'CODE', 'DEPTH_LEVEL')
    );

    $departments = array();

    while($ob = $sections->GetNextElement()){
        $arFields = $ob->GetFields();

        $department = array(
            'NAME' =>  $arFields['ID'],
            'TITLE' => $arFields['NAME']
        );

        array_push($departments, $department);
    }
    $arResult['DEPARTMENTS'] = $departments;

    //echo "<pre>";
    //print_r($arResult['DEPARTMENTS']);
    //echo "</pre>";

}