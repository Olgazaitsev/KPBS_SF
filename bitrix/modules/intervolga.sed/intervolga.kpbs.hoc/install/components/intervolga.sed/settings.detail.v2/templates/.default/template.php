<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arResult */
/** @var array $arParams */
?>

<?php if (!empty($arResult['LIST_BUTTONS'])): ?>
    <div class="adm-detail-toolbar">
        <?php foreach ($arResult['LIST_BUTTONS'] as $button): ?>
            <a href="<?php echo $button['URL'] ?>" class="adm-detail-toolbar-btn cts-settings__detail__list-btn"
               title="<?php echo $button['LABEL'] ?>">
                <span class="adm-detail-toolbar-btn-l"></span><span
                        class="adm-detail-toolbar-btn-text"><?php echo $button['LABEL'] ?></span><span
                        class="adm-detail-toolbar-btn-r"></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($arResult['MESSAGES'])): ?>
    <div class="adm-info-message-wrap<?php echo ($arResult['MESSAGES']['TYPE'] == 'SUCCESS') ? ' adm-info-message-green' : ' adm-info-message-red'; ?>">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?php echo $arResult['MESSAGES']['TITLE'] ?></div><?php echo $arResult['MESSAGES']['BODY'] ?>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($arResult['FIELDS'])): ?>
    <form method="POST" id="cts-settings__detail__form" enctype="multipart/form-data">
        <input type="hidden" name="sessid" value="<?php echo bitrix_sessid() ?>">
        <div class="adm-detail-block">
            <div class="adm-detail-content-wrap">
                <div class="adm-detail-content">
                    <div class="adm-detail-title"><?php echo $arResult['TITLE'] ?></div>
                    <div class="adm-detail-content-item-block">
                        <table class="adm-detail-content-table edit-table">
                            <tbody>
                            <?php foreach ($arResult['FIELDS'] as $fieldCode => $field): ?>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <span <?php if ($field['REQUIRED'] == 'Y') echo 'class="adm-required-field"'; ?>><?php echo $field['LABEL'] ?>
                                            :</span>
                                    </td>
                                    <td class="adm-detail-content-cell-r">
                                        <?php if ($field['EDITABLE'] != 'Y'): ?>
                                            <?php echo (string)$field['VALUE'] ?>
                                        <?php elseif ($field['TYPE'] == 'INPUT'): ?>
                                        <input type="text" name="<?php echo $fieldCode ?>" size="60"
                                               value="<?php echo $field['VALUE'] ?>">
                                        <?php elseif ($field['TYPE'] == 'SELECT'): ?>
                                            <select name="<?php echo $fieldCode ?>"
                                                    class="cts-settings__detail__select">
                                                <?php foreach ($field['OPTIONS'] as $option): ?>
                                                    <option value="<?php echo $option['VALUE'] ?>"
                                                        <?php if ($option['DISABLED'] == 'Y') {
                                                            echo ' disabled';
                                                        } elseif ($option['VALUE'] == $field['VALUE']) {
                                                            echo ' selected';
                                                        } ?>
                                                    ><?php echo $option['NAME'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($field['TYPE'] == 'COLOR'): ?>
                                        <?
                                        static $colorPickerId = 1;
                                        static $defaultColors = array(
                                            'BTN_COLOR' => '#BBEB21',
                                            'BTN_TEXT_COLOR' => '#535c69',
                                        );
                                        ?>
                                        <input type="text" id="<?= $fieldCode ?>" name="<?= $fieldCode ?>"
                                               value="<?= htmlspecialcharsbx($field['VALUE']) ?>" size="7"
                                               maxlength="7" style="float:left; margin-right: 5px">
                                            <script>
                                                function SetButtonColorInput<?= $fieldCode ?>(color) {
                                                    if (!color)
                                                        color = '<?=$defaultColors[$fieldCode]?>';
                                                    BX('<?= $fieldCode ?>').value = color;
                                                    BX('<?= $fieldCode ?>_label').style.background = color;
                                                }
                                            </script>
                                        <?php
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:main.colorpicker',
                                            '',
                                            Array(
                                                'SHOW_BUTTON' => 'Y',
                                                'ID' => $colorPickerId++,
                                                'NAME' => Loc::getMessage('SET_DET_V2_TPL.COLOR_SELECTION'),
                                                'ONSELECT' => "SetButtonColorInput{$fieldCode}"
                                            ),
                                            false
                                        );
                                        ?>
                                            <div class="btn-color-label" id="<?= $fieldCode ?>_label"
                                                 style="background: <?= htmlspecialcharsbx($field['VALUE']) ?>"></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="adm-detail-content-btns-wrap">
                    <div class="adm-detail-content-btns">
                        <input type="submit"
                               id="cts-settings__detail__save-btn"
                               value="<?php echo $arResult['SAVE_BTN']['LABEL'] ?>"
                               title="<?php echo $arResult['SAVE_BTN']['LABEL'] ?>"
                               class="adm-btn-save">
                        <a href="<?php echo $arResult['CANCEL_BTN']['URL'] ?>">
                            <input type="button"
                                   value="<?php echo $arResult['CANCEL_BTN']['LABEL'] ?>"
                                   title="<?php echo $arResult['CANCEL_BTN']['LABEL'] ?>">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>