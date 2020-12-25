<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Forum\ForumTable;

if (Loader::includeModule('forum')) {
    $dbForumTableValues = ForumTable::getList(array(
        'select' => array('ID', 'NAME')
    ));

    $forumTableValues = array();
    $defaultForumIdValue = '';

    while ($fetchedForumTableValues = $dbForumTableValues->fetch())
    {
        $formattedString = '[' . $fetchedForumTableValues['ID'] . '] ' . $fetchedForumTableValues['NAME'];
        $forumTableValues[$fetchedForumTableValues['ID']] = $formattedString;
    }
}

$moduleId = 'intervolga.sed';

$aTabs = array(
    array(
        'DIV' => 'intervolga_sed_options',
        'TAB' => Loc::getMessage('INTERVOLGA_SED_MAIN_TAB'),
        'OPTIONS' => array(
            Loc::getMessage('INTERVOLGA_SED_MAIN_SECTION'),
            array('intervolga_sed_forum_id',
                Loc::getMessage('INTERVOLGA_SED_CHOOSE_FORUM'),
                null,
                array('selectbox', $forumTableValues),
            ),
            array('note' => Loc::getMessage('INTERVOLGA_SED_FORUM_NODE')),
            array(
                'intervolga_sed_show_task_forums_on_detail_page',
                Loc::getMessage('INTERVOLGA_SED_SHOW_TASK_FORUMS_ON_DETAIL_PAGE'),
                'Y',
                array('checkbox')
            ),
            array(
                'intervolga_sed_contract_deal_code',
                Loc::getMessage('INTERVOLGA_SED_CONTRACT_DEAL_CODE'),
                null,
                array('text', 50)
            ),
            array(
                'intervolga_sed_deal_contract_file_code',
                Loc::getMessage('INTERVOLGA_SED_DEAL_CONTRACT_FILE_CODE'),
                null,
                array('text', 50)
            )
        )
    )
);

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>

    <form method="post" action="" name="bootstrap">
        <? $tabControl->Begin();

        foreach ($aTabs as $aTab)
        {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
        }

        $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>

        <?= bitrix_sessid_post(); ?>
        <? $tabControl->End(); ?>
    </form>

<?
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($_REQUEST['save']) > 0 && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab)
    {
        __AdmSettingsSaveOptions($moduleId, $aTab['OPTIONS']);
    }

    LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&mid_menu=1&mid=' . urlencode($moduleId) .
        '&tabControl_active_tab=' . urlencode($_REQUEST['tabControl_active_tab']));
}
?>