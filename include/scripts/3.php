<?php
use \Bitrix\Main\Loader;
use \Bitrix\Crm\CompanyTable;

Loader::includeModule('crm');

$deals = CompanyTable::getList([
    'filter' => [
        //'ID' => 6499
    ],
    'select' => [
        'ID', 'UF_CRM_1615472770'
    ]
]);

while ($arResDeals = $deals->fetch()) {
    print_r($arResDeals);
}