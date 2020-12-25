<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<script>
    BX.message({
        'SED_DET_JS.ANOTHER_EXT': '<?=GetMessage('SED_DET_JS.ANOTHER_EXT');?>',
        'SED_DET_JS.SELECT_FILE': '<?=GetMessage('SED_DET_JS.SELECT_FILE');?>',
        'SED_DET_JS.ENTER_COMMENT': '<?=GetMessage('SED_DET_JS.ENTER_COMMENT');?>'
    });
</script>
<?

/** @var array $arResult */
/** @var $APPLICATION */
/** @var $component */


if (!empty($arResult['CONTRACT_LIST_BTN'])) {
    $this->SetViewTarget('pagetitle');
    echo $arResult['CONTRACT_LIST_BTN'];
    $this->EndViewTarget();
}

if (!empty($arResult['ERRORS'])) {
    foreach ($arResult['ERRORS'] as $error) {
        echo $error;
    }
}
?>

<?php if ($arResult['CAN_UPDATE_FILE']): ?>
    <div class="task-detail">
        <div class="task-detail-info">
            <div class="task-detail-header">
                <div class="task-detail-header-title"><?= $arResult['FILE_UPLOAD_FORM']['TITLE'] ?></div>
            </div>
            <div class="task-detail-content">
                <form id="<?= $arResult['FILE_UPLOAD_FORM']['ID'] ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="sessid" value="<?php echo bitrix_sessid() ?>">
                    <input type="hidden"
                           name="<?= $arResult['FILE_UPLOAD_FORM']['HIDDEN_ACTION_INPUT']['NAME'] ?>"
                           value="<?= $arResult['FILE_UPLOAD_FORM']['HIDDEN_ACTION_INPUT']['VALUE'] ?>">
                    <table class="bx-interface-grid" width="100%">
                        <tr>
                            <td>
                                <textarea class="task-detail-property-value-textarea"
                                          id="<?= $arResult['FILE_UPLOAD_FORM']['COMMENT']['ID'] ?>"
                                          name="<?= $arResult['FILE_UPLOAD_FORM']['COMMENT']['NAME'] ?>"
                                          placeholder="<?= $arResult['FILE_UPLOAD_FORM']['COMMENT']['PLACEHOLDER'] ?>"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="button"
                                        class="task-view-button complete webform-small-button webform-small-button-accept"
                                        id="<?= $arResult['FILE_UPLOAD_FORM']['FILE_SELECT_BTN']['ID'] ?>"><?= $arResult['FILE_UPLOAD_FORM']['FILE_SELECT_BTN']['TEXT'] ?></button>
                                <button type="submit"
                                        class="task-view-button complete webform-small-button"
                                        id="<?= $arResult['FILE_UPLOAD_FORM']['FILE_UPLOAD_BTN']['ID'] ?>"><?= $arResult['FILE_UPLOAD_FORM']['FILE_UPLOAD_BTN']['TEXT'] ?></button>
                                <input class="hide"
                                       type="file"
                                       name="<?= $arResult['FILE_UPLOAD_FORM']['HIDDEN_FILE_INPUT']['NAME'] ?>"
                                       id="<?= $arResult['FILE_UPLOAD_FORM']['HIDDEN_FILE_INPUT']['ID'] ?>">
                                <br>
                            </td>
                        </tr>
                    </table>
                </form>

            </div>
        </div>
    </div>
<?php endif; ?>

<div class="task-detail">
    <div class="task-detail-info">
        <div class="task-detail-header">
            <div class="task-detail-header-title">
                <?= GetMessage('SED_DET_TPL.NEGOTIATION_PAGE'); ?> - <?= $arResult['CONTRACT_INFO']['NAME'] ?>
            </div>
        </div>
        <div class="task-detail-content">
            <table class="contract-detail-content" width="100%">
                <tr>
                    <td width="50%">
                        <table class="bx-interface-grid" width="100%">
                            <tr>
                                <td><?= GetMessage('SED_DET_TPL.INIT'); ?></td>
                                <td><a href="<?= $arResult['CONTRACT_INFO']['INITIATOR']['DETAIL_URL'] ?>"
                                       target="_blank"><?= $arResult['CONTRACT_INFO']['INITIATOR']['FULL_NAME'] ?></a>
                                </td>
                            </tr>
                            <tr>
                                <td><?= GetMessage('SED_DET_TPL.STATUS'); ?></td>
                                <td class="contract-status <?= $arResult['CONTRACT_INFO']['STATUS']['CSS_CLASS'] ?>"><?= $arResult['CONTRACT_INFO']['STATUS']['NAME'] ?></td>
                            </tr>
                            <tr>
                                <td><?= GetMessage('SED_DET_TPL.START'); ?></td>
                                <td><?= $arResult['CONTRACT_INFO']['HARMONIZATION_START'] ?></td>
                            </tr>
                            <tr>
                                <td><?= GetMessage('SED_DET_TPL.DAYS'); ?></td>
                                <td><?= $arResult['CONTRACT_INFO']['DAYS_TO_HARMONIZE'] ?></td>
                            </tr>
                            <? foreach ($arResult['CONTRACT_INFO']['USER_FIELDS'] as $userField): ?>
                                <tr>
                                    <td><?= $userField['EDIT_FORM_LABEL'] ?></td>
                                    <td><?
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:system.field.view',
                                            $userField['USER_TYPE']['USER_TYPE_ID'],
                                            array(
                                                'arUserField' => $userField
                                            )
                                        );
                                        ?></td>
                                </tr>
                            <? endforeach ?>
                        </table>
                    </td>
                    <td valign="middle" align="center">
                        <p><a <?=$arResult['CONTRACT_INFO']['FILE_VIEWER_ATTRIBUTES']?> href="<?= $arResult['CONTRACT_INFO']['FILE_DOWNLOAD_URL'] ?>"
                              ><?= GetMessage('SED_DET_TPL.CUR_VERSION'); ?></a></p>
                        <p>
                            <a href="<?= $arResult['CONTRACT_INFO']['MAIN_TASK_URL'] ?>"><?= GetMessage('SED_DET_TPL.MAIN_TASK'); ?></a>
                        </p>
                        <?php if (!empty($arResult['CONTRACT_INFO']['PDF_REQUEST_LINK'])): ?>
                            <p>
                                <a href="<?= $arResult['CONTRACT_INFO']['PDF_REQUEST_LINK'] ?>" target="_blank"
                                   class="task-view-button complete webform-small-button webform-small-button-accept">
                                    <?= GetMessage('SED_DET_TPL.DOWNLOAD_PDF'); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php if (!empty($arResult['CONTRACT_TASK_TABLE'])): ?>
    <div class="task-detail contract-table">
        <div class="task-detail-info">
            <div class="task-detail-content">
                <table class="bx-interface-grid" width="100%">
                    <?php foreach ($arResult['CONTRACT_TASK_TABLE'] as $row): ?>
                        <tr>
                            <td><?= $row['USER']['ROLE_NAME'] ?></td>
                            <td><a href="<?= $row['USER']['DETAIL_URL'] ?>"
                                   target="_blank"><?= $row['USER']['FULL_NAME'] ?></a></td>
                            <td class="contract-table-aligned-cell contract-table-status <?= $row['TASK']['STATUS_CLASS'] ?>"><?= $row['TASK']['STATUS_NAME'] ?></td>
                            <td class="contract-table-aligned-cell"><?php if ($row['TASK']['STATUS_CHANGED_DATE']) echo $row['TASK']['STATUS_CHANGED_DATE'] ?></td>
                            <td class="contract-table-aligned-cell">
                                <?php if ($row['TASK']['DETAIL_URL'] && $row['TASK']['DETAIL_URL_LABEL']): ?>
                                    <a href="<?= $row['TASK']['DETAIL_URL'] ?>"><?= $row['TASK']['DETAIL_URL_LABEL'] ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<div id="sed_contract_detail_footer">
    <div id="sed_contract_detail_forum_wrap">
        <?php foreach ($arResult['FORUMS'] as $forum): ?>
            <div class="task-detail">
                <div class="task-detail-info">
                    <div class="task-detail-h2">
                        <div class="task-detail-h2-title"><?= $forum['TITLE']; ?></div>
                    </div>
                </div>
            </div>
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:forum.comments",
                "",
                Array(
                    "ALLOW_ALIGN" => "Y",
                    "ALLOW_ANCHOR" => "Y",
                    "ALLOW_BIU" => "Y",
                    "ALLOW_CODE" => "Y",
                    "ALLOW_FONT" => "Y",
                    "ALLOW_HTML" => "Y",
                    "ALLOW_IMG" => "N",
                    "ALLOW_LIST" => "Y",
                    "ALLOW_MENTION" => "Y",
                    "ALLOW_NL2BR" => "Y",
                    "ALLOW_QUOTE" => "Y",
                    "ALLOW_SMILES" => "Y",
                    "ALLOW_TABLE" => "Y",
                    "ALLOW_VIDEO" => "N",
                    "CACHE_TIME" => "0",
                    "CACHE_TYPE" => "Y",
                    "DATE_TIME_FORMAT" => "d.m.Y H:i:s",
                    "EDITOR_CODE_DEFAULT" => "N",
                    "ENTITY_ID" => $forum['ENTITY_ID'],
                    "ENTITY_TYPE" => $forum['ENTITY_TYPE'],
                    "ENTITY_XML_ID" => $forum['ENTITY_XML_ID'],
                    "FORUM_ID" => $forum['FORUM_ID'],
                    "IMAGE_HTML_SIZE" => "0",
                    "IMAGE_SIZE" => "600",
                    "MESSAGES_PER_PAGE" => "10",
                    "NAME_TEMPLATE" => "",
                    "PAGE_NAVIGATION_TEMPLATE" => "",
                    "PERMISSION" => "M",
                    "PREORDER" => "N",
                    "SHOW_MINIMIZED" => "N",
                    "SHOW_RATING" => "Y",
                    "SUBSCRIBE_AUTHOR_ELEMENT" => "N",
                    "URL_TEMPLATES_PROFILE_VIEW" => "",
                    "URL_TEMPLATES_READ" => "",
                    "USER_FIELDS" => array("UF_FORUM_MESSAGE_DOC", "UF_FORUM_MESSAGE_VER", "UF_FORUM_MES_URL_PRV"),
                    "USE_CAPTCHA" => "Y"
                )
            ); ?>
        <?php endforeach; ?>
    </div>
</div>
