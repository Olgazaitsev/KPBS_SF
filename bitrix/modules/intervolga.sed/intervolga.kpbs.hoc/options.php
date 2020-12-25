<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Forum\ForumTable;
use Intervolga\Sed\Subscription\SubscriptionException;
use Intervolga\Sed\Subscription\SubscriptionService;

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

    try {
        $subscriptionService = new SubscriptionService();
        $subscriptionKey = $subscriptionService->getInstanceSubscriptionKey();
        $subscription = $subscriptionService->getInstanceSubscription();
        $subscriptionStatus = $subscriptionService->checkInstanceSubscription();
        switch ($subscriptionStatus) {
            case SubscriptionService::STATUS_OK:
                $subscriptionStatus = Loc::getMessage('IV_SED_SUB_STATUS_OK');
            break;

            case SubscriptionService::STATUS_EXPIRED:
                $subscriptionStatus = Loc::getMessage(
                        'IV_SED_SUB_STATUS_EXPIRED',
                        array('#DAYS#' => $subscription != null ? $subscription->getGraceDays() : '?')
                );
            break;

            case SubscriptionService::STATUS_USAGE_FORBIDDEN:
                $subscriptionStatus = Loc::getMessage('IV_SED_SUB_STATUS_USAGE_FORBIDDEN');
            break;
        }
    } catch (SubscriptionException $e) {
        $subscriptionStatus = Loc::getMessage('IV_SED_SUB_STATUS_USAGE_FORBIDDEN');
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
        )
    ),

    array(
            'DIV' => 'sed_subscription',
            'TAB' => Loc::getMessage('IV_SED_SUB_TAB'),
            'OPTIONS' => array(
                    array(
                            'subscriptionKey',
                            Loc::getMessage('IV_SED_SUB_KEY'),
                            $subscriptionKey ?? 'N/A',
                            array('statictext'),
                    ),
                    array(
                            'subscriptionHolder',
                            Loc::getMessage('IV_SED_SUB_HOLDER'),
                            $subscription ? $subscription->getHolder() : 'N/A',
                            array('statictext'),
                    ),
                    array(
                            'subscriptionContacts',
                            Loc::getMessage('IV_SED_SUB_CONTACTS'),
                            $subscription ? $subscription->getContacts() : 'N/A',
                            array('statictext'),
                    ),
                    array(
                            'subscriptionIssued',
                            Loc::getMessage('IV_SED_SUB_ISSUED'),
                            $subscription ? $subscription->getIssued()->format('Y-m-d') : 'N/A',
                            array('statictext'),
                    ),
                    array(
                            'subscriptionValidThrough',
                            Loc::getMessage('IV_SED_SUB_VALID_THROUGH'),
                            $subscription ? $subscription->getValidThrough()->format('Y-m-d') : 'N/A',
                            array('statictext'),
                    ),
                    array(
                            'subscriptionStatus',
                            Loc::getMessage('IV_SED_SUB_STATUS'),
                            $subscriptionStatus,
                            array('statictext'),
                    ),
                    array('note' => Loc::getMessage('IV_SED_SUB_HELP'))
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