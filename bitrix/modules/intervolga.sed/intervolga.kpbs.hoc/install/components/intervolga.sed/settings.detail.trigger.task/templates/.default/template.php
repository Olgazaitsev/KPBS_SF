<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arResult */
/** @var array $arParams */
?>
    <script>
        BX.message({
            'SET_DET_TRG_TPL.JS_BTN_ADD': '<?=GetMessage('SET_DET_TRG_TPL.JS_BTN_ADD');?>',
            'SET_DET_TRG_TPL.JS_LABEL_ADD': '<?=GetMessage('SET_DET_TRG_TPL.JS_LABEL_ADD');?>',
            'SET_DET_TRG_TPL.JS_BTN_DEL': '<?=GetMessage('SET_DET_TRG_TPL.JS_BTN_DEL');?>',
            'SET_DET_TRG_TPL.JS_OPTION_EMPTY': '<?=GetMessage('SET_DET_TRG_TPL.JS_OPTION_EMPTY');?>'
        });
    </script>
<?php if(!empty($arResult['LIST_BUTTONS'])):?>
    <div class="adm-detail-toolbar">
        <?php foreach($arResult['LIST_BUTTONS'] as $button):?>
            <a href="<?php echo $button['URL']?>" class="adm-detail-toolbar-btn sed-settings__task-trigger-detail__list-btn" title="<?php echo $button['LABEL']?>">
                <span class="adm-detail-toolbar-btn-l"></span><span
                        class="adm-detail-toolbar-btn-text"><?php echo $button['LABEL']?></span><span
                        class="adm-detail-toolbar-btn-r"></span>
            </a>
        <?php endforeach;?>
    </div>
<?php endif;?>

<?php if (!empty($arResult['MESSAGES'])): ?>
    <div class="adm-info-message-wrap<?php echo ($arResult['MESSAGES']['TYPE'] == 'SUCCESS') ? ' adm-info-message-green' : ' adm-info-message-red';?>">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?php echo $arResult['MESSAGES']['TITLE']?></div><?php echo $arResult['MESSAGES']['BODY']?>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
<?php endif;?>

<?php if(!empty($arResult['FIELDS'])):?>
    <form method="POST" id="sed-settings__task-trigger-detail__form" enctype="multipart/form-data">
        <input type="hidden" name="sessid" value="<?php echo bitrix_sessid()?>">
        <div class="adm-detail-block">
            <div class="adm-detail-content-wrap">
                <div class="adm-detail-content">
                    <div class="adm-detail-title"><?php echo $arResult['TITLE']?></div>
                    <div class="adm-detail-content-item-block">
                        <table class="adm-detail-content-table edit-table">
                            <tbody>
                            <?php foreach ($arResult['FIELDS'] as $fieldCode => $field):?>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <span <?php if($field['REQUIRED'] == 'Y') echo 'class="adm-required-field"';?>><?php echo $field['LABEL']?>:</span>
                                    </td>
                                    <td class="adm-detail-content-cell-r">
                                        <?php if ($field['EDITABLE'] != 'Y'):?>
                                            <?php echo (string)$field['VALUE']?>
                                        <?php elseif ($field['TYPE'] == 'INPUT'):?>
                                            <input type="text" name="<?php echo $fieldCode?>" size="60" value="<?php echo $field['VALUE']?>">
                                        <?php elseif ($field['TYPE'] == 'SELECT'):?>
                                        <?php if($field['IS_MULTIPLE'] == 'Y'): ?>
                                        <select name="<?php echo $fieldCode?>[]" class="sed-settings__task-trigger-detail__select" multiple size="<?php echo count($field['OPTIONS'])?>">
                                            <?php else: ?>
                                            <select name="<?php echo $fieldCode?>" class="sed-settings__task-trigger-detail__select">
                                                <?php endif; ?>
                                                <?php foreach ($field['OPTIONS'] as $option):?>
                                                    <option value="<?php echo $option['VALUE']?>"
                                                        <?php if($option['DISABLED'] == 'Y') {
                                                            echo ' disabled';
                                                        }
                                                        else if ($field['IS_MULTIPLE'] == 'Y') {
                                                            if(is_array($field['VALUE']) && in_array($option['VALUE'], $field['VALUE'])) {
                                                                echo ' selected';
                                                            }
                                                        }
                                                        else  if ($option['VALUE'] == $field['VALUE']) {
                                                            echo ' selected';
                                                        }
                                                        ?>
                                                    ><?php echo $option['NAME']?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <?php endif;?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td width="40%" class="adm-detail-content-cell-l"><?=Loc::getMessage('SET_DET_TRG_TPL.LABEL_ACTIONS');?></td>
                                <td id="sed-settings__actions-container"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="adm-detail-content-btns-wrap">
                    <div class="adm-detail-content-btns">
                        <input type="submit"
                               id="sed-settings__task-trigger-detail__save-btn"
                               value="<?php echo $arResult['SAVE_BTN']['LABEL']?>"
                               title="<?php echo $arResult['SAVE_BTN']['LABEL']?>"
                               class="adm-btn-save">
                        <a href="<?php echo $arResult['CANCEL_BTN']['URL']?>">
                            <input type="button"
                                   value="<?php echo $arResult['CANCEL_BTN']['LABEL']?>"
                                   title="<?php echo $arResult['CANCEL_BTN']['LABEL']?>">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif;?>