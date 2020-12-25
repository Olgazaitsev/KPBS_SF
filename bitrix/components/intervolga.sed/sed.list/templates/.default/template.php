<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arResult */
/** @var $APPLICATION */
/** @var $component */

if (!empty($arResult['PAGE_TITLE_BTN'])) {
    $this->SetViewTarget('inside_pagetitle');
    ?><div class="pagetitle-container pagetitle-align-right-container"><?
    echo $arResult['PAGE_TITLE_BTN'];
    ?></div><?
    $this->EndViewTarget();
}

if (!empty($arResult['ERRORS'])) {
    foreach ($arResult['ERRORS'] as $error) {
        echo $error;
    }
}
?>
<div class="contract-list-page">
    <div class="process-list-container">
        <?php foreach ($arResult['GRID_DATA'] as $gridData): ?>
            <div class="process-header-container">
                <div class="process-name">
                    <?php echo $gridData['PROCESS_NAME'] ?>
                </div>
                <div class="process-filter">
                    <? $APPLICATION->ShowViewContent($gridData['GRID_ID']); ?>
                </div>
            </div>
            <div class="grid-container">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:crm.interface.grid',
                    'titleflex',
                    array(
                        'RENDER_FILTER_INTO_VIEW' => $gridData['GRID_ID'],
                        'FILTER' => $gridData['FILTER'],
                        'FILTER_PRESETS' => $gridData['FILTER_PRESETS'],
                        'ENABLE_LIVE_SEARCH' => true,
                        'ENABLE_LABEL' => true,
                        'CACHE_TYPE' => 'N',
                        //уникальный идентификатор грида
                        'GRID_ID' => $gridData['GRID_ID'],
                        //описание колонок грида, поля типизированы
                        'HEADERS' => $gridData['HEADER'],
                        //сортировка
                        'SORT' => $gridData['SORT'],
                        //это необязательный
                        'SORT_VARS' => $gridData['SORT_VARS'],
                        //данные
                        'ROWS' => $gridData['ROWS'],
                        'SHOW_TOTAL_COUNTER' => true,
                        'TOTAL_ROWS_COUNT' => $gridData['NAV_OBJECT']->getRecordCount(),
                        'PAGINATION' => $gridData['PAGINATION'],
                        //групповые действия
                        'ACTIONS' => array(),
                        //разрешить действия над всеми элементами
                        'ACTION_ALL_ROWS' => false,
                        //разрешено редактирование в списке
                        'EDITABLE' => false,
                        //объект постранички
                        'NAV_OBJECT' => $gridData['NAV_OBJECT'],
                        'AJAX_MODE' => 'Y',
                        'AJAX_OPTION_HISTORY' => 'N',
                        'AJAX_OPTION_JUMP' => 'N',
                    ),
                    $component
                ); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>