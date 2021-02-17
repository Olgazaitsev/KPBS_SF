<?php

namespace kpbs\Custom;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;


class Application
{

    const DEAL_CATEGORY_PROJECT= 56;

    public static function init()
    {
        self::initJsHandlers();
        self::initEventHandlers();
    }

    protected static function initJsHandlers()
    {

    }

    public static function initEventHandlers()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler("crm"
            , "OnAfterCrmCompanyAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmCompanyAdd"));

        $eventManager->addEventHandler("crm"
            , "OnAfterCrmContactAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmContactAdd"));

        /*$eventManager->addEventHandler("crm"
            , "OnAfterCrmDealAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmDealAdd"));*/

        $eventManager->addEventHandler("crm"
            , "OnAfterCrmLeadAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmLeadAdd"));

//        $eventManager->addEventHandler('tasks','OnTaskAdd', ['\iTrack\Custom\Handlers\Tasks','onTaskAdd']);
//        $eventManager->addEventHandler('crm','OnBeforeCrmDealUpdate', ['\iTrack\Custom\Handlers\Crm','onBeforeCrmDealUpdate']);
//        $eventManager->addEventHandler('crm','OnAfterCrmDealUpdate', ['\iTrack\Custom\Handlers\Crm','onAfterCrmDealUpdate']);
//        $eventManager->addEventHandler('crm','OnAfterCrmDealAdd', ['\iTrack\Custom\Handlers\Crm','onAfterCrmDealAdd']);
//        $eventManager->addEventHandler('main','OnProlog', ['\iTrack\Custom\Handlers\Main','onProlog']);
          $eventManager->addEventHandler('main','OnEpilog', ['\iTrack\Custom\Handlers\Main','onEpilog']);
//        $eventManager->addEventHandler('im','OnBeforeMessageNotifyAdd', ['\iTrack\Custom\Handlers\Im','onBeforeMessageNotifyAdd']);
//        $eventManager->addEventHandler("crm", "OnAfterCrmTimelineCommentAdd", ['\iTrack\Custom\Handlers\Crm','funcOnAfterCrmTimelineCommentAdd']);
//        $eventManager->addEventHandler("crm", "OnAfterCrmTimelineCommentAdd", funcOnAfterCrmTimelineCommentAdd);

    }


    public static function log($msg, $file = 'main.log')
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/logs/' . $file, date(DATE_COOKIE) . ': ' . $msg . "\n", FILE_APPEND);
    }
}
