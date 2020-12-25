<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CJSCore::Init(array("jquery"));

/** @var array $arResult */
/** @var $APPLICATION */

global $APPLICATION;
$APPLICATION->SetTitle(\Bitrix\Main\Localization\Loc::getMessage('SED_ADD_TEMPLATE.START_PROCESS'));

if (!empty($arResult['PAGE_TITLE_BTN'])) {
    $this->SetViewTarget('pagetitle');
    echo $arResult['PAGE_TITLE_BTN'];
    $this->EndViewTarget();
}

if (!empty($arResult['ERRORS'])) {
    foreach ($arResult['ERRORS'] as $error) {
        echo $error;
    }
}
?>

<div class="sed-content">
    <div class="info-before-table opened">
        <div class="row">
            <div class="label"><?php echo \Bitrix\Main\Localization\Loc::getMessage('SED_ADD_TEMPLATE.CHOOSE_PROCESS') ?>
                <sup>*</sup></div>
            <div class="value">
                <select id="sed-process-selector">
                    <?php
                    foreach ($arResult['PROCESS_LIST'] as $processId => $processData): ?>
                        <option value="<?php echo $processId ?>" <?php if ($processId == $arResult['PROCESS_TO_DISPLAY']) echo 'selected="selected"'; ?>><?php echo $processData['NAME'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <?php foreach ($arResult['PROCESS_LIST'] as $processId => $processData): ?>
        <div class="sed-add-tab"
             data-process-id="<?php echo $processId ?>" <?php if ($processId == $arResult['PROCESS_TO_DISPLAY']) echo 'style="display: block"'; ?>>
            <form class="sed-add-form" data-process-id="<?php echo $processId ?>"
                  name="sed_add_form_<?php echo $processId ?>" method="post" enctype="multipart/form-data">
                <div class="info-before-table opened">
                    <input type="hidden" name="sessid" value="<?php echo bitrix_sessid() ?>">
                    <?php foreach ($processData['INPUT_LIST'] as $inputData): ?>
                        <?php if ($inputData['TYPE'] == 'HIDDEN'): ?>
                            <input type="hidden"
                                   class="sed-add-input sed-add-input-required"
                                   name="<?php echo $inputData['NAME'] ?>"
                                   value="<?php echo $inputData['DEFAULT'] ?>">
                        <?php elseif ($inputData['TYPE'] == 'TEXT'): ?>
                            <div class="row <?= $inputData['CLASS']; ?>">
                                <div class="label"><?php echo $inputData['LABEL'] ?><sup>*</sup></div>
                                <div class="value">
                                    <input type="text"
                                           class="sed-add-input sed-add-input-required"
                                           data-process-id="<?php echo $processId ?>"
                                           value="<?php echo $inputData['DEFAULT'] ?>"
                                           placeholder="<?php echo $inputData['PLACEHOLDER'] ?>"
                                           name="<?php echo $inputData['NAME'] ?>"/>
                                </div>
                            </div>
                        <?php elseif ($inputData['TYPE'] == 'FILE'): ?>
                            <div class="row <?= $inputData['CLASS']; ?>">
                                <div class="label"><?php echo $inputData['LABEL'] ?><sup>*</sup></div>
                                <div class="value">
                                    <input type="file"
                                           class="sed-add-input sed-add-input-required"
                                           data-process-id="<?php echo $processId ?>"
                                           value="<?php echo $inputData['DEFAULT'] ?>"
                                           name="<?php echo $inputData['NAME'] ?>"/>
                                </div>
                            </div>
                        <?php elseif ($inputData['TYPE'] == 'NUMBER' && $inputData['NAME'] != 'days_to_harmonize'): ?>
                            <div class="row <?= $inputData['CLASS']; ?>">
                                <div class="label"><?php echo $inputData['LABEL'] ?><sup>*</sup></div>
                                <div class="value">
                                    <input type="number"
                                           class="sed-add-input sed-add-input-required"
                                           data-process-id="<?php echo $processId ?>"
                                           min="1"
                                           value="<?php echo $inputData['DEFAULT'] ?>"
                                           name="<?php echo $inputData['NAME'] ?>"/>
                                </div>
                            </div>
                        <?php elseif ($inputData['TYPE'] == 'NUMBER' && $inputData['NAME'] == 'days_to_harmonize'): ?>
                            <input type="hidden"
                                   class="sed-add-input sed-add-input-required"
                                   data-process-id="<?php echo $processId ?>"
                                   min="1"
                                   value="<?php echo $inputData['DEFAULT'] ?>"
                                   name="<?php echo $inputData['NAME'] ?>"/>
                        <?php elseif ($inputData['TYPE'] == 'USER'): ?>
                            <div class="row <?= $inputData['CLASS']; ?>">
                                <div class="label"><?php echo $inputData['LABEL'] ?><sup>*</sup></div>
                                <div class="value user-role-container"
                                     data-role-id="<?php echo $inputData['ROLE_ID'] ?>">
                                    <input type="text"
                                           class="sed-add-input user-role-input sed-add-input-required"
                                           value="<?php echo (empty($inputData['DEFAULT'])) ? '' : $inputData['DEFAULT']['NAME'] . ' ' . $inputData['DEFAULT']['LAST_NAME']; ?>"
                                           data-process-id="<?php echo $processId ?>"
                                           readonly="readonly"
                                           placeholder=""/>
                                    <input type="hidden"
                                           class="sed-add-input user-role-input-hidden sed-add-input-required"
                                           name="<?php echo $inputData['NAME'] ?>"
                                           value="<?php echo $inputData['DEFAULT']['ID'] ?>"
                                           data-process-id="<?php echo $processId ?>"
                                           readonly="readonly"/>
                                    <div class="popup user-role-popup">
                                        <?php echo \Intervolga\Sed\Tools\Html::getUserRolePopup($inputData['DEFAULT']['ID'], 'PARTICIPANT_' . $inputData['ROLE_ID'], 'SedComponentAdd.onPopupValueChanged'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($inputData['TYPE'] == 'CRM'): ?>
                            <div class="row <?= $inputData['CLASS']; ?>">
                                <div class="label"><?php echo $inputData['LABEL'] ?><sup>*</sup></div>
                                <div class="value user-role-container"
                                     data-role-id="<?php echo $inputData['ROLE_ID'] ?>">
                                    <? $APPLICATION->IncludeComponent(
                                        'bitrix:crm.entity.selector',
                                        '',
                                        array(
                                            'ENTITY_TYPE' => 'COMPANY',
                                            'INPUT_NAME' => $inputData['NAME'],
                                            'INPUT_VALUE' => isset($_REQUEST[$inputData['NAME']]) ? $_REQUEST[$inputData['NAME']] : '',
                                            'MULTIPLE' => 'N'
                                        ),
                                        null,
                                        array('HIDE_ICONS' => 'Y')
                                    ); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div class="row <?= $inputData['CLASS']; ?>">
                        <div class="label">Целевая дата согласования договора<sup>*</sup></div>
                        <div class="value">
                            <input id="END_DATE_I"
                                   type="date"
                                   class="sed-add-input sed-add-input-required"
                                   data-process-id="<?php echo $processId ?>"
                                   onchange="$('#hid_text_end_date').val(this.value);"
                                   value=""
                                   name="END_DATE"/>
                        </div>
                        <input id="hid_text_end_date"
                               type="text"
                               name="hid_text_end_date"
                               maxlength="255"
                               value=""
                               style="visibility: hidden"/>
                    </div>


                    <? foreach ($processData['USER_FIELDS_INPUT_LIST'] as $userField): ?>
                        <? if ($userField['SHOW'] == 'Y'): ?>
                            <div class="row">
                                <div class="label">
                                    <?= $userField['EDIT_FORM_LABEL'] ?>
                                    <? if ($userField['MANDATORY'] == 'Y'): ?>
                                        <sup>*</sup>
                                    <? endif ?>
                                </div>
                                <div class="user-field-value sed-add-input<? if ($userField['MANDATORY'] == 'Y'): ?> sed-add-input-required<? endif ?>"
                                     data-process-id="<?= $processId ?>"
                                     data-user-type-id="<?= $userField['USER_TYPE']['USER_TYPE_ID'] ?>"
                                     data-field-name="<?= $userField['FIELD_NAME'] ?>">
                                    <? $APPLICATION->IncludeComponent(
                                        'bitrix:system.field.edit',
                                        $userField['USER_TYPE']['USER_TYPE_ID'],
                                        array(
                                            'arUserField' => $userField,
                                            'form_name' => "sed_add_form_$processId",
                                        )
                                    ); ?>
                                </div>
                            </div>
                        <? endif ?>
                    <? endforeach ?>
                </div>
                <div class="webform-buttons">
                    <button class="webform-small-button webform-small-button-accept" type="submit"
                            value="<?php echo $arResult['SUBMIT_BTN_INFO']['VALUE'] ?>"
                            name="<?php echo $arResult['SUBMIT_BTN_INFO']['NAME'] ?>">
                        <span class="webform-small-button-text"><?php echo $arResult['SUBMIT_BTN_INFO']['LABEL'] ?></span>
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>