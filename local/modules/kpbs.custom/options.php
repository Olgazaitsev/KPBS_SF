<?php

$MODULE_ID = 'kpbs.custom';

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

global $USER;
if (!$USER->CanDoOperation($MODULE_ID . '_settings')) {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

if(!Loader::includeModule('iblock')) {
    ShowError(Loc::GetMessage($MODULE_ID."_MODULE_iblock_NOT_INSTALLED"));
    return;
}

$arIblocks = [];
$dbIblock = \Bitrix\Iblock\IblockTable::query()
    ->setSelect(['ID','NAME'])
    ->exec();
while($arIBlock = $dbIblock->fetch()) {
    $arIblocks[$arIBlock['ID']] = '['.$arIBlock['ID'].']: '.$arIBlock['NAME'];
}

$arAllOptions = [
    'main' => [
        /*[
            'main_ftp',
            Loc::getMessage($MODULE_ID.'_FTP'),
            Option::get($MODULE_ID, 'FTP'),
            ['text']
        ],
        [
            'main_ftpl',
            Loc::getMessage($MODULE_ID.'_FTPL'),
            Option::get($MODULE_ID, 'FTPL'),
            ['text']
        ],
        [
            'main_ftpp',
            Loc::getMessage($MODULE_ID.'_FTPP'),
            Option::get($MODULE_ID, 'FTPP'),
            ['text']
        ],*/
        [
            'main_uf',
            Loc::getMessage($MODULE_ID.'_UF'),
            Option::get($MODULE_ID, 'UF'),
            ['text']
        ],
        /*[
            'webinars_iblock_id',
            Loc::getMessage($MODULE_ID.'_webinars_iblock_id'),
            Option::get($MODULE_ID, 'webinars_iblock_id'),
            [
                'multiselectbox',
                $arIblocks
            ]
        ],*/
    ],
    'kpi' => [
        [
            'm1_val',
            Loc::getMessage($MODULE_ID.'_m1_val'),
            Option::get($MODULE_ID, 'm1_val'),
            ['text']
        ],
        [
            'm2_val',
            Loc::getMessage($MODULE_ID.'_m2_val'),
            Option::get($MODULE_ID, 'm2_val'),
            ['text']
        ],
        [
            'm3_val',
            Loc::getMessage($MODULE_ID.'_m3_val'),
            Option::get($MODULE_ID, 'm3_val'),
            ['text']
        ],
        [
            'm4_val',
            Loc::getMessage($MODULE_ID.'_m4_val'),
            Option::get($MODULE_ID, 'm4_val'),
            ['text']
        ],
        [
            'm5_val',
            Loc::getMessage($MODULE_ID.'_m5_val'),
            Option::get($MODULE_ID, 'm5_val'),
            ['text']
        ],
        [
            'm6_val',
            Loc::getMessage($MODULE_ID.'_m6_val'),
            Option::get($MODULE_ID, 'm6_val'),
            ['text']
        ],
        [
            'w1_val',
            Loc::getMessage($MODULE_ID.'_w1_val'),
            Option::get($MODULE_ID, 'w1_val'),
            ['text']
        ],
        [
            'w2_val',
            Loc::getMessage($MODULE_ID.'_w2_val'),
            Option::get($MODULE_ID, 'w2_val'),
            ['text']
        ],
        [
            'w3_val',
            Loc::getMessage($MODULE_ID.'_w3_val'),
            Option::get($MODULE_ID, 'w3_val'),
            ['text']
        ],
        [
            'w4_val',
            Loc::getMessage($MODULE_ID.'_w4_val'),
            Option::get($MODULE_ID, 'w4_val'),
            ['text']
        ],
        [
            'w5_val',
            Loc::getMessage($MODULE_ID.'_w5_val'),
            Option::get($MODULE_ID, 'w5_val'),
            ['text']
        ],
        [
            'w6_val',
            Loc::getMessage($MODULE_ID.'_w6_val'),
            Option::get($MODULE_ID, 'w6_val'),
            ['text']
        ],
        [
            'kb_id',
            Loc::getMessage($MODULE_ID.'_kb_id'),
            Option::get($MODULE_ID, 'kb_id'),
            ['text']
        ],
        [
            'cn_id',
            Loc::getMessage($MODULE_ID.'_cn_id'),
            Option::get($MODULE_ID, 'cn_id'),
            ['text']
        ],
        [
            'cl_id',
            Loc::getMessage($MODULE_ID.'_cl_id'),
            Option::get($MODULE_ID, 'cl_id'),
            ['text']
        ],
        [
            'pl_id',
            Loc::getMessage($MODULE_ID.'_pl_id'),
            Option::get($MODULE_ID, 'pl_id'),
            ['text']
        ],
        [
            'mk_id',
            Loc::getMessage($MODULE_ID.'_mk_id'),
            Option::get($MODULE_ID, 'mk_id'),
            ['text']
        ],
        [
            'mb_id',
            Loc::getMessage($MODULE_ID.'_mb_id'),
            Option::get($MODULE_ID, 'mb_id'),
            ['text']
        ],
        [
            'mp_id',
            Loc::getMessage($MODULE_ID.'_mp_id'),
            Option::get($MODULE_ID, 'mp_id'),
            ['text']
        ],
        [
            'ib_id',
            Loc::getMessage($MODULE_ID.'_ib_id'),
            Option::get($MODULE_ID, 'ib_id'),
            ['text']
        ],
        [
            'ib_bon_id',
            Loc::getMessage($MODULE_ID.'_ib_bon_id'),
            Option::get($MODULE_ID, 'ib_bon_id'),
            ['text']
        ],
        [
            'ib_uu_id',
            Loc::getMessage($MODULE_ID.'_ib_uu_id'),
            Option::get($MODULE_ID, 'ib_uu_id'),
            ['text']
        ],
        [
            'q1',
            Loc::getMessage($MODULE_ID.'_q1'),
            Option::get($MODULE_ID, 'q1'),
            ['text']
        ],
        [
            'q2',
            Loc::getMessage($MODULE_ID.'_q2'),
            Option::get($MODULE_ID, 'q2'),
            ['text']
        ],
        [
            'q3',
            Loc::getMessage($MODULE_ID.'_q3'),
            Option::get($MODULE_ID, 'q3'),
            ['text']
        ],
        [
            'q4',
            Loc::getMessage($MODULE_ID.'_q4'),
            Option::get($MODULE_ID, 'q4'),
            ['text']
        ]
    ]
];

//$ufarr = array("НИЧЕГО НЕ ВЫБРАНО", "FORMATTED_OPPORTUNITY", "OPPORTUNITY_WITH_CURRENCY", "OPPORTUNITY");

$ufarr['EMPTY'] = 'НИЧЕГО НЕ ВЫБРАНО';
$ufarr["FORMATTED_OPPORTUNITY"] = "FORMATTED_OPPORTUNITY";
$ufarr["OPPORTUNITY_WITH_CURRENCY"] = "OPPORTUNITY_WITH_CURRENCY";
$ufarr["OPPORTUNITY"] = "OPPORTUNITY";

$rsUserFields = \Bitrix\Main\UserFieldTable::getList(array(
    'filter' => array('ENTITY_ID' => 'CRM_DEAL')
));


while($arUserField=$rsUserFields->fetch())
{
    if($arUserField['USER_TYPE_ID']=='double' || $arUserField['USER_TYPE_ID']=='text') {
        //array_push($ufarr, $arUserField['FIELD_NAME']);
        $ufarr[$arUserField['FIELD_NAME']] = $arUserField['FIELD_NAME'];
    }

}

$res = \Bitrix\Main\GroupTable::getList(
    array(
        // выбераем название, идентификатор, символьный код, сортировку
        'select' => array('NAME', 'ID', 'STRING_ID', 'C_SORT'),
        // все группы, кроме основной группы администраторов
        'filter' => array('!ID' => '1')
    )
);
//print_r($res->Fetch());

while ($arResGroup = $res->Fetch()) {
    //echo "<pre>";
    //print_r($arResContact);
    //echo "</pre>";

    $grouparr = [
          'group_'.$arResGroup['ID'],
           $arResGroup['ID'].'.'.$arResGroup['NAME'],
           Option::get($MODULE_ID, 'group_'.$arResGroup['ID']),
            [
                'multiselectbox',
                $ufarr
            ]
    ];
    array_push($arAllOptions['main'], $grouparr);
}

$userarr = [];


$rsUser = \CUser::GetList(($by="ID"), ($order="desc"), хъ);
// заносим прочие показатели
$users = array();

while ($arResUser = $rsUser->Fetch()) {
    $userarr[$arResUser['ID']] = $arResUser['NAME'].' '.$arResUser['LAST_NAME'];
}

$userset = [
    'users_list',
    Loc::getMessage($MODULE_ID.'_users_list'),
    Option::get($MODULE_ID, 'users_list'),
    [
        'multiselectbox',
        $userarr
    ]
];
array_push($arAllOptions['kpi'], $userset);

if(isset($request["save"]) && check_bitrix_sessid()) {
    foreach ($arAllOptions as $part) {
        foreach($part as $arOption) {
            if(is_array($arOption)) {
                __AdmSettingsSaveOption($MODULE_ID, $arOption);
            }
        }
    }
}

$arTabs = [
    [
        "DIV" => "main",
        "TAB" => Loc::getMessage($MODULE_ID.'_main'),
        "ICON" => $MODULE_ID . '_settings',
        "TITLE" => Loc::getMessage($MODULE_ID.'_bizon365_tab_subtitle'),
        'TYPE' => 'options', //options || rights || user defined
    ],
    [
        "DIV" => "kpi",
        "TAB" => Loc::getMessage($MODULE_ID.'_kpi'),
        "ICON" => $MODULE_ID . '_settings',
        "TITLE" => Loc::getMessage($MODULE_ID.'_kpi'),
        'TYPE' => 'options', //options || rights || user defined
    ]
];

$tabControl = new CAdminTabControl("tabControl", $arTabs);

$tabControl->Begin();
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&amp;lang=<?= LANG ?>"
      name="<?= $MODULE_ID ?>_settings">
    <?= bitrix_sessid_post(); ?>
    <?
    foreach ($arTabs as $tab) {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($MODULE_ID, $arAllOptions[$tab['DIV']]);
    }?>
    <?$tabControl->Buttons();?>
    <input type="submit" class="adm-btn-save" name="save" value="<?=Loc::getMessage($MODULE_ID.'_save');?>">
    <?=bitrix_sessid_post();?>
    <? $tabControl->End(); ?>
</form>
