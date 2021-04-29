<?php
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

echo "<pre>";
print_r($targetgroup);
echo "</pre>";