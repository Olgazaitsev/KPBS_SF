<?php
use \Bitrix\Main\Loader;
use \Bitrix\Crm\DealTable;

Loader::includeModule('crm');

$deals = DealTable::getList([
    'filter' => [
        //'ID' => 6499
    ],
    'select' => [
        'ID', 'UF_CRM_1579077455200'
    ]
]);

while ($arResDeals = $deals->fetch()) {
    print_r($arResDeals);
}
