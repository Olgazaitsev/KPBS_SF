<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arResult */
/** @var array $arParams */
/** @var CMain $APPLICATION */
?>
    <script>
        BX.message({
            'SET_DET_TRG_TPL.JS_BTN_CHANGE': '<?=GetMessage('SET_DET_TRG_TPL.JS_BTN_CHANGE');?>',
            'SET_DET_TRG_TPL.JS_BTN_DEL': '<?=GetMessage('SET_DET_TRG_TPL.JS_BTN_DEL');?>'
        });
    </script>
<?php if (!empty($arResult['ERROR_MSG'])): ?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?php echo $arResult['ERROR_MSG']['TITLE'] ?></div><?php echo $arResult['ERROR_MSG']['BODY'] ?>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
<?php else: ?>
    <form method="get" id="cts-settings__list__form">
        <input type="hidden" name="sessid" value="<?php echo bitrix_sessid() ?>">
        <div class="adm-filter-wrap">
            <table class="adm-filter-main-table">
                <tbody>
                <tr>
                    <td class="adm-filter-main-table-cell">
                        <div class="adm-filter-tabs-block">
                            <span class="adm-filter-tab adm-filter-tab-active"><?php echo $arResult['FILTER']['TITLE'] ?></span>
                            <span class="adm-filter-tabs-block-underlay"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="adm-filter-main-table-cell">
                        <div class="adm-filter-content">
                            <div class="adm-filter-content-table-wrap">
                                <table cellspacing="0" class="adm-filter-content-table">
                                    <tbody>
                                    <?php foreach ($arResult['FILTER']['FIELDS'] as $fieldCode => $field): ?>
                                        <tr>
                                            <td class="adm-filter-item-left"><?php echo $field['LABEL'] ?></td>
                                            <td class="adm-filter-item-center">
                                                <?php if ($field['TYPE'] == 'SELECT'): ?>
                                                    <div class="adm-filter-alignment">
                                                        <div class="adm-filter-box-sizing">
                                                            <?php if ($field['MULTIPLE'] == 'Y'): ?>
                                                                <span class="adm-select-wrap-multiple">
                                                                <select name="<?php echo $fieldCode ?>[]"
                                                                        class="adm-select-multiple cts-settings__list__filter-input cts-settings__list__select"
                                                                        multiple
                                                                        size="<?php echo (count($field['OPTIONS']) < 10) ? count($field['OPTIONS']) : '10' ?>">
                                                                    <?php foreach ($field['OPTIONS'] as $option): ?>
                                                                        <option value="<?php echo $option['VALUE'] ?>"
                                                                            <?php
                                                                            if ($option['DISABLED'] == 'Y') {
                                                                                echo ' disabled';
                                                                            } elseif ($option['VALUE'] === $field['VALUE']) {
                                                                                echo ' selected';
                                                                            } ?>
                                                                        ><?php echo $option['NAME'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </span>
                                                            <?php else: ?>
                                                                <span class="adm-select-wrap">
                                                                <select name="<?php echo $fieldCode ?>"
                                                                        class="adm-select cts-settings__list__filter-input cts-settings__list__select">
                                                                    <option value=""><?php echo $arResult['DEFAULT_OPTION'] ?></option>
                                                                    <?php foreach ($field['OPTIONS'] as $option): ?>
                                                                        <option value="<?php echo $option['VALUE'] ?>"
                                                                            <?php
                                                                            if ($option['DISABLED'] == 'Y') {
                                                                                echo ' disabled';
                                                                            } elseif ($option['VALUE'] === $field['VALUE']) {
                                                                                echo ' selected';
                                                                            } ?>
                                                                        ><?php echo $option['NAME'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <input class="cts-settings__list__filter-input" type="text"
                                                           name="<?php echo $fieldCode ?>" size="60"
                                                           value="<?php echo $field['VALUE'] ?>">
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="adm-filter-bottom">
                                <input type="submit" class="adm-btn"
                                       title="<?php echo $arResult['FILTER']['SEARCH_BTN_LABEL'] ?>"
                                       value="<?php echo $arResult['FILTER']['SEARCH_BTN_LABEL'] ?>">
                                <input type="button" class="adm-btn" id="cts-settings__list__cancel-filter"
                                       title="<?php echo $arResult['FILTER']['SEARCH_CANCEL_BTN_LABEL'] ?>"
                                       value="<?php echo $arResult['FILTER']['SEARCH_CANCEL_BTN_LABEL'] ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="adm-list-table-layout">
            <div class="adm-list-table-wrap">
                <div class="adm-list-table-top">
                    <?php if ($arResult['TABLE']['ADD_ITEM_BTN']['URL'] && $arResult['TABLE']['ADD_ITEM_BTN']['LABEL']): ?>
                        <a href="<?php echo $arResult['TABLE']['ADD_ITEM_BTN']['URL'] ?>"
                           class="adm-btn adm-btn-save adm-btn-add"><?php echo $arResult['TABLE']['ADD_ITEM_BTN']['LABEL'] ?></a>
                    <?php endif; ?>
                </div>
                <table class="adm-list-table">
                    <thead>
                    <?php if (!empty($arResult['TABLE']['HEADERS'])): ?>
                        <tr class="adm-list-table-header">
                            <td class="adm-list-table-cell">
                                <div class="adm-list-table-cell-inner"></div>
                            </td>
                            <?php foreach ($arResult['TABLE']['HEADERS'] as $fieldCode => $header): ?>
                                <td class="adm-list-table-cell cts-settings__list__field-header<?php if ($header['SORTED']) {
                                    echo ($header['SORTED'] == 'DESC') ? ' adm-list-table-cell-sort-down' : ' adm-list-table-cell-sort-up';
                                } ?><?php if ($header['SORTABLE'] == 'Y') {
                                    echo ' adm-list-table-cell-sort';
                                } ?>"
                                    data-sort-by="<?php echo $fieldCode ?>"
                                    title="<?php echo $header['LABEL'] ?>">
                                    <div class="adm-list-table-cell-inner"><?php echo $header['LABEL'] ?></div>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endif; ?>
                    </thead>
                    <tbody>
                    <?php if (!empty($arResult['TABLE']['DATA'])): ?>
                        <?php foreach ($arResult['TABLE']['DATA'] as $taskType): ?>
                            <tr class="adm-list-table-row cts-settings__list__table-row"
                                data-id="<?php echo $taskType['ID']['VALUE'] ?>">
                                <td class="adm-list-table-cell adm-list-table-popup-block">
                                    <div class="adm-list-table-popup"
                                         title="<?= Loc::getMessage('SET_DET_TRG_TPL.LABEL_ACTIONS'); ?>"></div>
                                </td>
                                <?php foreach ($taskType as $item): ?>
                                    <td class="adm-list-table-cell">
                                        <?php if ($item['TYPE'] == 'TEXT'): ?>
                                            <?php echo $item['VALUE'] ?>
                                        <?php elseif ($item['TYPE'] == 'URL'): ?>
                                            <a href="<?php echo $item['URL'] ?>"><?php echo $item['VALUE'] ?></a>
                                        <?php elseif ($item['TYPE'] == 'COLOR'): ?>
                                            <div class="btn-color-label"
                                                 name="<?php echo $fieldCode ?>"
                                                 style="background: <?= htmlspecialcharsbx($item['VALUE']) ?>"></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo(count($arResult['TABLE']['HEADERS']) + 1) ?>"
                                class="adm-list-table-cell adm-list-table-empty"><?php echo Loc::getMessage('SET_DET_TRG_TPL.LABEL_NO_DATA') ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (is_array($arResult['PAGINATION']) && count($arResult['PAGINATION'])): ?>
                <div class="adm-navigation">
                    <div class="adm-nav-pages-block">
                        <?php if ($arResult['PAGINATION']['PAGE_NUMBER'] == 1): ?>
                            <span class="adm-nav-page adm-nav-page-prev"></span>
                        <?php else: ?>
                            <a class="adm-nav-page adm-nav-page-prev"
                               href="<?php echo $arResult['PAGINATION']['PREV_PAGE_LINK'] ?>"></a>
                        <?php endif; ?>
                        <?php foreach ($arResult['PAGINATION']['LINKS'] as $link): ?>
                            <?php if ($link['VALUE'] == $arResult['PAGINATION']['PAGE_NUMBER']): ?>
                                <span class="adm-nav-page-active adm-nav-page"><?php echo $link['VALUE'] ?></span>
                            <?php else: ?>
                                <a href="<?php echo $link['HREF'] ?>"
                                   class="<?php echo ($link['VISIBLE'] === false) ? 'adm-nav-page-separator' : 'adm-nav-page' ?>"><?php echo $link['VALUE'] ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($arResult['PAGINATION']['PAGE_NUMBER'] == $arResult['PAGINATION']['MAX_PAGE_NUMBER']): ?>
                            <span class="adm-nav-page adm-nav-page-next"></span>
                        <?php else: ?>
                            <a class="adm-nav-page adm-nav-page-next"
                               href="<?php echo $arResult['PAGINATION']['NEXT_PAGE_LINK'] ?>"></a>
                        <?php endif; ?>
                    </div>
                    <?php if ($arResult['PAGINATION']['TOTAL_BLOCK']['CNT']): ?>
                        <div class="adm-nav-pages-total-block"><?php echo Loc::getMessage('SET_DET_TRG_TPL.PAGINATION_TOTAL_BLOCK', array('#FROM#' => $arResult['PAGINATION']['TOTAL_BLOCK']['FROM'], '#TO#' => $arResult['PAGINATION']['TOTAL_BLOCK']['TO'], '#CNT#' => $arResult['PAGINATION']['TOTAL_BLOCK']['CNT'])); ?></div>
                    <?php endif; ?>
                    <div class="adm-nav-pages-number-block">
                        <span class="adm-nav-pages-number">
                            <span class="adm-nav-pages-number-text"><?= Loc::getMessage('SET_DET_TRG_TPL.ON_PAGE'); ?></span>
                            <span class="adm-select-wrap">
                                <select name="" class="adm-select cts-settings__list__pagination-size-select">
                                    <option disabled="" hidden=""></option>
                                    <?php foreach ($arResult['PAGINATION']['ALLOWED_PAGE_SIZES'] as $allowedPageSizeValue => $allowedPageSizeName): ?>
                                        <option value="<?php echo $allowedPageSizeValue ?>"<?php echo ($allowedPageSizeValue == $arResult['PAGINATION']['PAGE_SIZE']) ? ' selected="selected"' : '' ?>><?php echo $allowedPageSizeName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <input type="hidden" id="cts-settings__list__sort-by" name="BY" value="<?php echo $arResult['SORT']['BY'] ?>">
        <input type="hidden" id="cts-settings__list__sort-order" name="ORDER"
               value="<?php echo $arResult['SORT']['ORDER'] ?>">
        <?php if (is_array($arResult['EXTRA_INPUTS']) && count($arResult['EXTRA_INPUTS'])): ?>
            <?php foreach ($arResult['EXTRA_INPUTS'] as $input): ?>
                <input type="hidden" name="<?php echo $input['NAME'] ?>" value="<?php echo $input['VALUE'] ?>">
            <?php endforeach; ?>
        <?php endif; ?>
    </form>
<?php endif; ?>