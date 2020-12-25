<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

// !!!*******************************
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/crm/classes/general/kpbs_fields_access.php");
$userId = CUser::GetID();
$userGroups = CUser::GetUserGroup($userId);
$FIELDS_ACCESS_DENIED = $GLOBALS["FIELDS_ACCESS_DENIED"];
$userAssignedDealsIds = Utility::GetUserAssignedByDealsIds($userId);
// !!!*******************************

// prepare and some for compatibility

$arResult['ITEMS']['dropzones'] = array();

foreach ($arResult['ITEMS']['columns'] as $k => &$column)
{
	if ($column['dropzone'])
	{
		$arResult['ITEMS']['dropzones'][] = array(
			'id' => $column['id'],
			'name' => $column['name'],
			'color' => $column['color'],
			'data' => array(
				'type' => $column['type']
			)
		);
		unset($arResult['ITEMS']['columns'][$k]);
	}
	else
	{
		$column = array(
			'id' => $column['id'],
			'total' => (int) $column['count'],
			'color' => $column['color'],
			'name' => htmlspecialcharsback($column['name']),
			'canAddItem' => $column['canAddItem'],
			'canSort' => $arResult['ACCESS_CONFIG_PERMS'] &&
						!($column['type'] == 'WIN' || $column['type'] == 'LOOSE'),
			'data' => array(
				'sort' => $column['sort'],
				'type' => $column['type'],
				'sum' => round($column['total']),
				'sum_init' => 0,
				'sum_format' => $column['total_format']
			)
		);
	}
}
unset($column);

foreach ($arResult['ITEMS']['items'] as $i => &$item)
{
	$item = array(
		'id' => $item['id'],
		'columnId' => $item['columnId'],
		'countable' => !isset($item['countable']) || $item['countable'],
		'droppable' => !isset($item['droppable']) || $item['droppable'],
		'draggable' => !isset($item['draggable']) || $item['draggable'],
		'data' => $item
	);
}
unset($item);

// !!!*******************************
foreach ($FIELDS_ACCESS_DENIED as $item) {
    if (in_array($item["GroupId"], $userGroups)) {
        foreach ($arResult['ITEMS']['columns'] as $k => &$column)
        {
            unset($column["total"]);
            unset($column["data"]["sum"]);
            unset($column["data"]["sum_init"]);
        }
        unset($column);

        foreach ($arResult['ITEMS']['items'] as $i => &$item)
        {
            $item["total"] = 0;

            if(!in_array($item["data"]['id'], $userAssignedDealsIds)) {
                $item["data"]["price"] = 0;
                $item["data"]["price_formatted"] = 0;

                foreach ($item["data"]["fields"] as $f => &$field) {
                    if (in_array($item["UserField"], $field["code"])) {
                        $field["value"] = 0;
                    }
                }
            }
        }
        unset($item);
    }
}
// !!!*******************************
